<?php

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
            'updated_at' => $this->updated_at,
            'recipients_count' => $this->whenCounted('recipients'),
            'recipients' => PostUserRecipientsResource::collection($this->whenLoaded('recipients')),
            'posted_by' => new UserPostedByResource($this->postedBy),
        ];
    }
}
