<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MentionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The Comment instance.
     *
     * @var Model
     */
    public Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('You were mentioned in a post!')
            ->greeting('Hello!')
            ->line('You were mentioned in a new post by '.$this->model->user->name.'.')
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable): array
    {
        // The instance `$this->model` represent the `Comment` model.
        $username = $this->model->user->username;
        $modelId = $this->model->getKey();

        $message = "<strong>@{ $username }</strong> has mentionned your name in his post!";

        return [
            'message' => $message,
            'postId' => $modelId,
            'type' => 'mention',
        ];
    }

    /**
     * @param  object  $notifiable
     * @return string
     */
    public function databaseType(object $notifiable): string
    {
        return 'post-mentioned';
    }
}
