<?php

namespace App\Http\Controllers\Api\Concern;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

trait CanLogoutOtherDevices
{
    public function logoutOtherDevicesSession(Request $request)
    {
        $request->user()->tokens()->where('id', '!=', Str::of(auth()->user()->currentAccessToken()->id)->explode('|')[0])->delete();
    }
}
