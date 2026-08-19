<?php

namespace App\Notifications\Welcome;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * A class defined for the complete profile
 */
class CompleteProfileNotification extends Notification implements ShouldQueue
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
            ->subject('Complete Your Profile')
            ->view('team', [
                'title' => 'Welcome back!',
                'description' => "To help us match you with the best opportunities and build trust, please complete your profile to 100%. The more complete and clear your profile is, the better your visibility! We're always here for you, Moawen Team",
                'url' => 'https://moawen.sa',
                'button_title' => 'Complete Profile Now',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
