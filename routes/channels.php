<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('server.post.new', fn () => true);
Broadcast::channel('session.logout.{userId}', function (User $user, $userId) {
    return $user->id === $userId;
});
