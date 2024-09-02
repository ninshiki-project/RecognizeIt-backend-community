<?php

namespace App\Events\Broadcast;

use App\Models\Posts;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewPostEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private Posts $post;

    public function __construct(Posts $posts)
    {
        $this->post = $posts;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('server.post.new'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'new.post';
    }

    public function broadcastWith(): array
    {
        return [
            'message' => 'New Post Received!',
            'meta' => [
                'post_id' => $this->post->id,
                'post_by' => [
                    'id' => $this->post->originalPoster->id,
                    'name' => $this->post->originalPoster->name,
                    'avatar' => $this->post->originalPoster->avatar,
                ],
            ],
        ];
    }
}
