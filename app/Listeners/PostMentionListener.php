<?php

namespace App\Listeners;

use App\Http\Services\Mention\MentionParser;
use App\Notifications\MentionNotification;
use MarJose123\NinshikiEvent\Events\Post\PostMentionUser;

class PostMentionListener
{
    public function __construct() {}

    public function handle(PostMentionUser $event): void
    {
        $mentions = (new MentionParser)->parse($event->post)->toCollection();
        $mentions->each(function ($mention) use ($event) {
            $mention->notify(new MentionNotification($event->post));
        });
    }
}
