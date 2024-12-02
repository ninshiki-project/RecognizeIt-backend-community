<?php

/*
 * Copyright (c) 2024.
 *
 * Filename: InvitationNotification.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Notifications\User\Invitation;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class InvitationNotification extends Notification
{
    public string $frontEndUri;

    public function __construct()
    {
        $this->frontEndUri = config('frontend.url');
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
            ->subject('You are invited to join '.config('app.name').' - Join Our Recognition System Today!')
            ->greeting('Hello!')
            ->line('We are thrilled to extend to you an invitation to join our cutting-edge Recognition System! 🌟')
            ->line('We believe in celebrating the exceptional contributions of every member of our team. Our Recognition System is designed to shine a spotlight on your hard work, dedication, and achievements within our community.')
            ->line(new HtmlString('<strong>Key Features of Our Ninshiki System:</strong>'))
            ->with(new HtmlString('1. <strong>User Profiles:</strong> Allow users to create profiles to track their progress and achievements.'))
            ->with(new HtmlString('2. <strong>Recognition Badges:</strong> Design badges for completing tasks, achieving milestones, or receiving recognition.'))
            ->with(new HtmlString('3. <strong>Leaderboards:</strong> Display top performers based on recognition received or other metrics.'))
            ->with(new HtmlString('4. <strong>Customizable Recognition Categories:</strong> Allow recognition in various categories with their own badges and rewards.'))
            ->with(new HtmlString('5. <strong>Points and Rewards System:</strong> Assign points for recognition and let users redeem them for rewards.'))
            ->with(new HtmlString('6. <strong>Real-time Notifications:</strong> Notify users of received recognition, level ups, or new badges.'))
            ->with(new HtmlString('7. <strong>Peer-to-Peer Recognition:</strong> Enable employees to recognize their peer contributions.'))
            ->with(new HtmlString('8. <strong>Integration with Communication Tools:</strong> Integrate with platforms like Slack or Microsoft Teams for seamless recognition.'))
            ->with(new HtmlString('9. <strong>Analytics and Reporting:</strong> Provide insights into recognition trends and top performers.'))
            ->with(new HtmlString('10. <strong>Training and Onboarding:</strong> Offer tutorials and resources for understanding the recognition system.'))
            ->with(new HtmlString('11. <strong>Social Sharing:</strong> Allow users to share achievements on social media.'))
            ->with(new HtmlString('12. <strong>Seasonal Events and Challenges:</strong> Organize special events or challenges for added engagement.'))
            ->with(new HtmlString('13. <strong>Feedback Mechanism:</strong> Gather user feedback to continuously improve the recognition system.'))
            ->with(new HtmlString('14. <strong>Accessibility and Inclusivity:</strong> Ensure the system is accessible to all users for inclusivity.'))
            ->with(new HtmlString('15. <strong>Shop for Reward Redemption:</strong> Allow users to redeem their earned points for rewards in a virtual shop.'))
            ->line('Joining our Recognition System is simple! Just click the button below to get started:')
            ->action('Visit', $this->frontEndUri)
            ->line("By joining, you'll not only be part of a culture of appreciation and support but also contribute to fostering a positive work environment where everyone's efforts are acknowledged and valued.")
            ->line('Thank you for using our application!');
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
