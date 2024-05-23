<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concern\CanValidateProvider;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginViaEmailRequest;
use App\Http\Resources\ProfileResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    use CanValidateProvider;

    public string $url;

    /**
     * Login via Provider
     *
     * @param  string  $provider  Possible options: zoho
     * @return JsonResponse
     *
     * @unauthenticated
     */
    public function loginViaProvider(string $provider)
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
                    'provider' => $provider,
                ])
                ->stateless()->redirect()->getTargetUrl();
        }

        return response()->json([
            'success' => (bool) $this->url,
            'link' => $this->url ?? '',
        ]);

    }

    /**
     * Login Provider Callback
     *
     * @return JsonResponse|void
     *
     * @unauthenticated
     */
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
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
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

                $token = $userCreated->createToken($request->device_name ?? 'nanshiki')->plainTextToken;

                return response()->json([
                    'success' => true,
                    'token' => [
                        //@var string Token for authentication.
                        //@example 31|b2da4411aa4e6d153d6725a17c672b8177c071e60a05158ff19af75a3b5829aa
                        'accessToken' => $token,
                    ],
                    'user' => new ProfileResource($userCreated),
                ]);

            }
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ]);
        }

    }

    /**
     * Login using Credentials
     *
     * @return JsonResponse
     *
     * @unauthenticated
     */
    public function loginViaEmail(LoginViaEmailRequest $request)
    {
        $user = User::where('email', $request->email)->whereNotNull('password')->first();
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken($request->device_name ?? 'nanshiki')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => [
                'accessToken' => $token,
            ],
            'user' => new ProfileResource($user),
        ]);

    }

    /**
     * Logout Session
     *
     * @return JsonResponse
     */
    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();

        return response()->json('', Response::HTTP_ACCEPTED);
    }
}
