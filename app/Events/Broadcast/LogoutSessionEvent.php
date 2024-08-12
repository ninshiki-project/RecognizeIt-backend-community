<?php

namespace App\Events\Broadcast;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LogoutSessionEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public User $user) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('session.logout.'.$this->user->id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'session.logout.other.device';
    }

    public function broadcastWith(): array
    {
        return [
            'message' => 'Session Logout Other Devices',
            'session_check' => route('session.health'),
        ];
    }
}
