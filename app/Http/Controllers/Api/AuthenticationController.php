<?php

/*
 * Copyright (c) 2024.
 *
 * Filename: AuthenticationController.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concern\AllowedDomain;
use App\Http\Controllers\Api\Concern\CanValidateProvider;
use App\Http\Controllers\Api\Enum\UserEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginViaEmailRequest;
use App\Http\Resources\ProfileResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use MarJose123\NinshikiEvent\Events\Session\UserLogin;
use MarJose123\NinshikiEvent\Events\Session\UserLogout;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AuthenticationController extends Controller
{
    use AllowedDomain;
    use CanValidateProvider;

    public string $url;

    /**
     * Request Provider Login Link
     *
     * @param  string  $provider  Possible options: zoho
     * @return JsonResponse
     *
     * @unauthenticated
     */
    public function loginViaProvider(string $provider)
    {
        $this->validateProvider($provider);

        if ($provider === 'zoho') {
            // @phpstan-ignore-next-line
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
     * Login via Provider Code
     *
     * @param  string  $provider
     * @param  Request  $request
     * @return JsonResponse|void
     *
     * @throws Throwable
     */
    public function providerCallback(string $provider, Request $request)
    {
        $this->validateProvider($provider);

        $request->validate([
            'code' => ['required', 'string'],
        ]);

        try {
            if ($provider === 'zoho') {
                // Get Access token from the code generated
                // @phpstan-ignore-next-line
                $tokenRequest = Socialite::driver($provider)->stateless()->getAccessTokenResponse($request->code);
                if (Arr::has($tokenRequest, 'error')) {
                    return response()->json([
                        'success' => false,
                        'message' => $tokenRequest['error'],
                    ], Response::HTTP_UNPROCESSABLE_ENTITY);
                }
                $accessToken = Arr::get($tokenRequest, 'access_token');
                // @phpstan-ignore-next-line
                $userProvider = Socialite::driver($provider)->stateless()->userFromToken($accessToken);

                if (! $this->isWhitelistedDomain($userProvider->email)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized email domain, please try again later.',
                    ], Response::HTTP_UNAUTHORIZED);
                }
                $user = User::where('email', $userProvider->email)->first();
                if (! $user) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized email or User does not exist.',
                    ], Response::HTTP_UNAUTHORIZED);
                }

                $this->userStatusValidate($user);

                $user->providers()->updateOrCreate(
                    [
                        'provider' => $provider,
                        'provider_id' => $userProvider->id,
                    ],
                    [
                        'avatar' => $userProvider->avatar,
                    ]
                );

                $user->name = $userProvider->name;
                if (! $user->avatar) {
                    $user->avatar = $userProvider->avatar ?? null;
                }
                $user->save();

                $token = $user->createToken($request->header('User-Agent'))->plainTextToken;

                /**
                 * Dispatch event for the user login
                 */
                UserLogin::dispatch($user);

                return response()->json([
                    'success' => true,
                    'token' => [
                        //@var string Token for authentication.
                        //@format 31|b2da4411aa4e6d153d6725a17c672b8177c071e60a05158ff19af75a3b5829aa
                        'accessToken' => $token,
                    ],
                    'user' => new ProfileResource($user),
                ], Response::HTTP_OK);

            }
        } catch (Throwable $throwable) {
            Log::info($throwable->getMessage());
            throw new $throwable;
        }

    }

    /**
     * Login using Credentials
     *
     * @return JsonResponse
     *
     * @unauthenticated
     *
     * @deprecated use login via providers
     */
    public function loginViaEmail(LoginViaEmailRequest $request)
    {
        $user = User::where('email', $request->email)->whereNotNull('password')->first();
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $this->userStatusValidate($user);

        $token = $user->createToken($request->header('User-Agent') ?? 'unknown')->plainTextToken;

        /**
         * Dispatch event for the user login
         */
        UserLogin::dispatch($user);

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
        /** @var \Laravel\Sanctum\PersonalAccessToken $token */
        $token = $request->user()->currentAccessToken();
        $token->delete();

        /**
         * Dispatch event for user logout
         */
        UserLogout::dispatch($request->user());

        return response()->json('', Response::HTTP_ACCEPTED);
    }

    /**
     * @param  User  $user
     * @return JsonResponse|null
     */
    public function userStatusValidate(User $user): ?JsonResponse
    {
        if ($user->status != UserEnum::Active) {
            if ($user->status == UserEnum::Invited) {
                // update the status to active
                $user->status = UserEnum::Active->value;
                $user->save();
            }

            if ($user->status == UserEnum::Deactivate) {
                return response()->json([
                    'success' => false,
                    'message' => 'The user account has been disabled from accessing the application.',
                ], Response::HTTP_UNAUTHORIZED);
            }

            return response()->json([
                'success' => false,
                'message' => 'User is not active.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return null;
    }
}
