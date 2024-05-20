<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    /**
     * Session Profile
     *
     * @return JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user()->load(['roles.permissions', 'notifications']));
    }
}
