<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('server.post.new', \App\Broadcasting\NewPostChannel::class);
