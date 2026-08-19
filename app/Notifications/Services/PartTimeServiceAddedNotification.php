<?php

namespace App\Notifications\Services;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * A class defined for part-time service added successfully message
 */
class PartTimeServiceAddedNotification extends Notification implements ShouldQueue
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
            ->subject('Part-Time Service Added Successfully')
            ->view('team', [
                'title' => 'Part-Time Service Live',
                'description' => 'Great job! Your part-time service has been successfully published on Moawen. Clients can now discover and hire you for ongoing projects. Get ready to build long-term professional relationships.',
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
