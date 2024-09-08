<?php

namespace App\Observers;

use App\Events\Broadcast\NewPostEvent;
use App\Models\Posts;

class PostsObserver
{
    public function created(Posts $posts): void
    {
        /**
         * Send Broadcast Event for the new post
         */
        NewPostEvent::dispatch($posts);

    }

    public function updated(Posts $posts): void {}

    public function deleted(Posts $posts): void {}

    public function restored(Posts $posts): void {}
}
