<?php

/*
 * Copyright (c) 2024.
 *
 * Filename: ProfileResource.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\User */
class ProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'avatar' => $this->avatar,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'department' => $this->department,
            'job_title' => $this->designation,
            'role' => $this->roles->flatten()->pluck('name')->toArray(),
            'email_verified_at' => $this->email_verified_at,
            'notifications_count' => [
                'unread' => $this->unreadNotifications->count(),
            ],
            'providers' => $this->mergeWhen($this->provider, $this->providers),
        ];
    }
}
