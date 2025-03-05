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

use App\Http\Controllers\Api\Enum\UserEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginViaEmailRequest;
use App\Http\Resources\ProfileResource;
use App\Http\Services\Facades\ZohoFacade;
use App\Http\Services\Zoho\Zoho;
use App\Models\User;
use Illuminate\Auth\Events\Logout;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use MarJose123\NinshikiEvent\Events\Session\UserLogin;
use MarJose123\NinshikiEvent\Events\Session\UserLogout;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AuthenticationController extends Controller
{
    use Concern\Zoho;

    public string $url;

    public array|JsonResponse|null $payload;

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

        if ($provider === 'zoho' && $this->isZohoSocialiteInstalled()) {
            $this->url = ZohoFacade::getLoginLink();
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
     *
     * @unauthenticated
     */
    public function providerCallback(string $provider, Request $request)
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        try {
            if ($provider === 'zoho' && $this->isZohoSocialiteInstalled()) {
                $this->payload = $this->performZohoAuthentication($request);
            } else {
                throw new \Exception('Invalid Provider');
            }

            if (! is_array($this->payload)) {
                return $this->payload;
            }

            /**
             * Dispatch event for the user login
             */
            UserLogin::dispatch($this->payload['user']);

            return response()->json([
                'success' => true,
                'token' => [
                    // @var string Token for authentication.
                    // @format 31|b2da4411aa4e6d153d6725a17c672b8177c071e60a05158ff19af75a3b5829aa
                    'accessToken' => $this->payload['token'],
                ],
                'user' => new ProfileResource($this->payload['user']),
            ], Response::HTTP_OK);

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
        event(new Logout('web', $request->user()));

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
