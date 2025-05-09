<?php

/*
 * Copyright (c) 2024.
 *
 * Filename: RecognizedNotification.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Notifications;

use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RecognizedNotification extends Notification
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param  object  $notifiable
     * @return string[]
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * @param  object  $notifiable
     * @return MailMessage
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->greeting('Hi '.$this->user->name.' 👋')
            ->line("Just wanted to drop you a quick note to say congrats! I heard through the grapevine that your efforts in the app haven't gone unnoticed. It's awesome to see your work getting recognized by the team. Keep it up!")
            ->action('View Post!', config('frontend.url'));
    }

    /**
     * @param  object  $notifiable
     * @return string[]
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Congratulations on Your Recognition!',
            'message' => "You've been recognized by your team/colleague in the app! Keep up the great work!",
        ];
    }

    /**
     * @param  object  $notifiable
     * @return string
     */
    public function databaseType(object $notifiable): string
    {
        return 'post-recognized';
    }
}
