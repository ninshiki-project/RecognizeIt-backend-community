<?php

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

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->greeting('Hey!')
            ->line("Just wanted to drop you a quick note to say congrats! I heard through the grapevine that your efforts in the app haven't gone unnoticed. It's awesome to see your work getting recognized by the team. Keep it up!")
            ->action('Visit', config('frontend.url'));
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'Congratulations on Your Recognition!',
            'message' => "You've been recognized by your team/colleague in the app! Keep up the great work!",
        ];
    }

    public function databaseType(object $notifiable): string
    {
        return 'post-recognized-by';
    }
}
