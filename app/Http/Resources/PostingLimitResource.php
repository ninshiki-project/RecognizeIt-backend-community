<?php

/*
 * Copyright (c) 2024.
 *
 * Filename: PostingLimitResource.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Http\Resources;

use App\Models\PostingLimit;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin PostingLimit */
class PostingLimitResource extends JsonResource
{
    /**
     * @param  Request  $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'limit' => $this->limit,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'designations_id' => $this->designations_id,

            'designations' => new DesignationsResource($this->whenLoaded('designations')),
        ];
    }
}
