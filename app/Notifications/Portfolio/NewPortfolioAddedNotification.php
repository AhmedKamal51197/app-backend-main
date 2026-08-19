<?php

namespace App\Notifications\Portfolio;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * A class defined for new portfolio added message
 */
class NewPortfolioAddedNotification extends Notification implements ShouldQueue
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
            ->subject('Portfolio Item Added Successfully')
            ->view('team', [
                'title' => 'Portfolio Updated',
                'description' => 'Excellent! Your new portfolio item has been successfully added to your profile. Showcase your best work to attract more clients and demonstrate your expertise. Keep building your impressive portfolio.',
                'url' => 'https://moawen.sa/profile',
                'button_title' => 'View Portfolio',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
