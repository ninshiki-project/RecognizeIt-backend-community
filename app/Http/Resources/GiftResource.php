<?php

/*
 * Copyright (c) 2025.
 *
 * Filename: GiftResource.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Http\Resources;

use App\Models\Gift;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Gift */
class GiftResource extends JsonResource
{
    /**
     * @param  Request  $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'gift' => $this->gift,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'by' => new PostUserRecipientsResource($this->whenLoaded('by')),
            'to' => new PostUserRecipientsResource($this->whenLoaded('to')), //
        ];
    }
}
