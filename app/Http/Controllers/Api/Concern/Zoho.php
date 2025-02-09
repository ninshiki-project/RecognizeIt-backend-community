<?php

/*
 * Copyright (c) 2025.
 *
 * Filename: Zoho.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Http\Controllers\Api\Concern;

use App\Http\Services\Facades\ZohoFacade;
use App\Models\User;
use Composer\InstalledVersions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

trait Zoho
{
    use AllowedDomain;

    public function isZohoSocialiteInstalled(): bool
    {
        return InstalledVersions::isInstalled('socialiteproviders/zoho');
    }

    public function performZohoAuthentication(Request $request): array|JsonResponse
    {
        $tokenRequest = ZohoFacade::setCode($request->code)->performCallBackAction();
        if (Arr::has($tokenRequest, 'error')) {
            return response()->json([
                'success' => false,
                'message' => $tokenRequest['error'],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $accessToken = Arr::get($tokenRequest, 'access_token');
        // @phpstan-ignore-next-line
        $userProvider = ZohoFacade::setAccessToken($accessToken)->getUserInfo();

        // @phpstan-ignore-next-line
        if (! $this->isWhitelistedDomain($userProvider->email)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized email domain, please try again later.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        // @phpstan-ignore-next-line
        $user = User::where('email', $userProvider->email)->first();
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized email or User does not exist.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $this->userStatusValidate($user);

        /**
         * Retrieve Zoho User Profile Picture
         */
        $avatar = ZohoFacade::getAvatar();
        $user->providers()->updateOrCreate(
            [
                'provider' => ZohoFacade::getProviderId(),
                // @phpstan-ignore-next-line
                'provider_id' => $userProvider->id,
            ],
            [
                // @phpstan-ignore-next-line
                'avatar' => $userProvider->avatar,
            ]
        );

        // @phpstan-ignore-next-line
        $user->name = $userProvider->name;
        $user->username = Str::slug($userProvider->name, '_');
        if (! $user->avatar) {
            $user->avatar = ! is_null($userProvider->avatar) ? $userProvider->avatar : $avatar ?? null;
        }
        $user->save();

        $token = $user->createToken($request->header('User-Agent'))->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
}
