<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\User */
class PostUserRecipientsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->user->id,
            'avatar' => $this->user->avatar,
            'name' => $this->user->name,
            'email' => $this->user->email,
        ];
    }
}
