<?php

namespace App\Notifications\Services;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * A class defined for one-time service added successfully message
 */
class OneTimeServiceAddedNotification extends Notification implements ShouldQueue
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
            ->subject('One-Time Service Added Successfully')
            ->view('team', [
                'title' => 'Service Published',
                'description' => 'Congratulations! Your one-time service has been successfully added to Moawen. Your service is now live and visible to potential clients. Start receiving orders and grow your business with us.',
                'url' => 'https://moawen.sa/profile',
                'button_title' => 'View My Services',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
