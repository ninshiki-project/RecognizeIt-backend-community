<?php
/*
 * Copyright (c) 2024.
 *
 * Filename: ResetPasswordNotification.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class ResetPasswordNotification extends Notification
{
    public string $url;

    /**
     * @param  string  $url
     */
    public function __construct(string $url)
    {
        $this->url = $url;
    }

    /**
     * @param  object  $notifiable
     * @return string[]
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * @param  object  $notifiable
     * @return MailMessage
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(Lang::get('Reset Password Notification'))
            ->line(Lang::get('You are receiving this email because we received a password reset request for your account.'))
            ->action(Lang::get('Reset Password'), $this->url)
            ->line(Lang::get('This password reset link will expire in :count minutes.', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]))
            ->line(Lang::get('If you did not request a password reset, no further action is required.'));
    }

    /**
     * @param  object  $notifiable
     * @return array
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
