<?php

/*
 * Copyright (c) 2024.
 *
 * Filename: CanLogoutOtherDevices.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Http\Controllers\Api\Concern;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

trait CanLogoutOtherDevices
{
    /**
     * @param  Request  $request
     * @return void
     */
    public function logoutOtherDevicesSession(Request $request): void
    {
        $token = $request->user()->tokens();
        /** @var \Laravel\Sanctum\PersonalAccessToken $token */
        $currentUserToken = auth()->user()->currentAccessToken();
        $tokenId = optional($currentUserToken)->id;
        $token->where('id', '!=', Str::of($tokenId)->explode('|')[0])->delete();
    }
}
