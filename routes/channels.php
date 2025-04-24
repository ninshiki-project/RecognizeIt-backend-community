<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('server.post.new', fn () => true);
Broadcast::channel('session.health.check', fn () => true);
Broadcast::channel('session.logout.{userId}', function (User $user, int $userId) {
    return $user->id === $userId;
});
