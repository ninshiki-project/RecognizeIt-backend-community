<?php

namespace App\Http\Controllers\Api\Concern;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

trait CanLogoutOtherDevices
{
    public function logoutOtherDevicesSession(Request $request)
    {
        $token = $request->user()->tokens();
        /** @var \Laravel\Sanctum\PersonalAccessToken $token */
        $currentUserToken = auth()->user()->currentAccessToken();
        $tokenId = optional($currentUserToken)->id;
        $token->where('id', '!=', Str::of($tokenId)->explode('|')[0])->delete();
    }
}
