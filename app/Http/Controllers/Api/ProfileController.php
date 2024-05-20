<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProfileResource;

class ProfileController extends Controller
{
    /**
     * Session Profile
     *
     * @return ProfileResource
     */
    public function me()
    {
        return new ProfileResource(auth()->user());
    }
}
