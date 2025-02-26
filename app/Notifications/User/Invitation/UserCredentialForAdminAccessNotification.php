<?php

/*
 * Copyright (c) 2025.
 *
 * Filename: UserCredentialForAdminAccessNotification.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Notifications\User\Invitation;

use Filament\Exceptions\NoDefaultPanelSetException;
use Filament\Facades\Filament;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class UserCredentialForAdminAccessNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $appName = 'Ninshiki';

    public string $appUrl;

    /**
     * @throws NoDefaultPanelSetException
     */
    public function __construct(private readonly string $tempPassword)
    {
        $this->appName = config('app.name');
        $this->appUrl = Filament::getDefaultPanel()->getLoginUrl();
    }

    /**
     * @param  mixed  $notifiable
     * @return string[]
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Welcome to {$this->appName} - Your Temporary Password")
            ->greeting("Dear {$notifiable->name},")
            ->line("Welcome to {$this->appName}! We're excited to have you onboard.")
            ->line('This email contains your temporary password to help you get started:')
            ->line('')
            ->line(new HtmlString("<strong>Email:</strong> {$notifiable->email}"))
            ->line(new HtmlString("<strong>Temporary Password:</strong> {$this->tempPassword}"))
            ->line('')
            ->line('For security reasons, we recommend that you change your password as soon as possible after logging in. You can do so by navigating to the "Account Settings" section once you\'re logged in.')
            ->action('Login to Ninshiki Server', $this->appUrl)
            ->line('Thank you for joining us, and we look forward to providing you with an amazing experience!')
            ->success();
    }
}
