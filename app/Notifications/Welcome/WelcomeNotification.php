<?php

namespace App\Notifications\Welcome;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * A class defined for the welcome message
 */
class WelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to Moawen!!')
            ->view('team', [
                'title' => 'Welcome to Moawen',
                'description' => 'Were excited to have you on board. Start by completing your profile and explore how we can help you launch your projects or offer your services with full safety and trust. Need help? Were here to support you anytime',
                'url' => 'https://moawen.sa',
                'button_title' => 'Complete Your Profile',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
