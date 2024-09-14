<?php
/*
 * Copyright (c) 2024.
 *
 * Filename: PostResource.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Posts */
class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'attachment_type' => $this->attachment_type,
            'attachment_url' => $this->attachment_url,
            'type' => $this->type,
            'created_at' => $this->created_at,
            'created_at_formatted' => $this->created_at->diffForHumans(),
            'updated_at' => $this->updated_at,
            'updated_at_formatted' => $this->updated_at->diffForHumans(),
            'posted_by' => new UserPostedByResource($this->originalPoster),
            'is_liked' => $this->isLikedBy(auth()->user()),
            'recipients' => PostUserRecipientsResource::collection($this->whenLoaded('recipients')),
            'liked_by' => UserPostedByResource::collection($this->likers),
        ];
    }
}
