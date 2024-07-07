<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\PostLike */
class PostLikeResource extends JsonResource
{
    public $with = ['user'];

    public function toArray(Request $request): array
    {
        return [
            'posts_id' => $this->post_id,
            'user' => new PostLikeUserResource($this->user),
        ];
    }

}
