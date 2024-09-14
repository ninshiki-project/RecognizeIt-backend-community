<?php
/*
 * Copyright (c) 2024.
 *
 * Filename: PostsObserver.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

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
