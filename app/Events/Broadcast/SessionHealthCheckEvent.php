<?php

namespace App\Events\Broadcast;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SessionHealthCheckEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct() {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('session.health.check'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'session.heartbeat.check';
    }

    public function broadcastWith(): array
    {
        return [
            'message' => 'Session Health Check',
            'session_check' => route('session.health'),
        ];
    }
}
