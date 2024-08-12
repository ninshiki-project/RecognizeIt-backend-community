<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('server.post.new', fn () => true);
