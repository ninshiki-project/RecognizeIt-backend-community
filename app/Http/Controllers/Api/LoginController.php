<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concern\CanValidateProvider;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    use CanValidateProvider;

    public string $url;

    public function loginViaProvider($provider)
    {
        $validated = $this->validateProvider($provider);
        if (! is_null($validated)) {
            return $validated;
        }

        if ($provider == 'zoho') {
            $this->url = Socialite::driver($provider)
                ->setScopes(['AaaServer.profile.Read'])
                ->with([
                    'prompt' => 'consent',
                    'access_type' => 'offline',

                ])
                ->stateless()->redirect()->getTargetUrl();
        }

        return response()->json([
            'success' => (bool) $this->url,
            'link' => $this->url ?? '',
        ]);

    }

    public function providerCallback($provider, Request $request)
    {
        $validated = $this->validateProvider($provider);
        if (! is_null($validated)) {
            return $validated;
        }

        if (is_null($request->code)) {
            return response()->json([
                'success' => false,
                'message' => 'Code is required',
            ], 422);
        }

        try {
            if ($provider === 'zoho') {
                // Get Access token from the code generated
                $tokenRequest = Socialite::driver($provider)->stateless()->getAccessTokenResponse($request->code);
                if (Arr::has($tokenRequest, 'error')) {
                    return response()->json([
                        'success' => false,
                        'message' => $tokenRequest['error'],
                    ]);
                }
                $accessToken = Arr::get($tokenRequest, 'access_token');

                $userProvider = Socialite::driver($provider)->stateless()->userFromToken($accessToken);
                $userCreated = User::firstOrCreate(
                    [
                        'email' => $userProvider->email,
                    ],
                    [
                        'email_verified_at' => Carbon::now(),
                        'name' => $userProvider->name,
                        'avatar' => $userProvider->avatar,
                    ]
                );
                $userCreated->providers()->updateOrCreate(
                    [
                        'provider' => $provider,
                        'provider_id' => $userProvider->id,
                    ],
                    [
                        'avatar' => $userProvider->avatar,
                    ]
                );

                // Create login in Sanctum

                $token = $userCreated->createToken($request->device_name ?? 'nanshiki')->plainTextToken;

                return response()->json([
                    'success' => true,
                    'token' => $token,
                    'user' => $userCreated,
                ]);

            }
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ]);
        }

    }

    public function index()
    {

    }

    public function store(Request $request)
    {
    }

    public function show($id)
    {
    }

    public function update(Request $request, $id)
    {
    }

    public function destroy($id)
    {
    }
}
