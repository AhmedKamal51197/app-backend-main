<?php

namespace App\Notifications\Offer;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * A class defined for offer added successfully message
 */
class OfferAddedNotification extends Notification implements ShouldQueue
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
            ->subject('Offer Sent Successfully')
            ->view('team', [
                'title' => 'Offer Sent',
                'description' => 'Great news! Your offer has been sent successfully to the client. Stay tuned for their response and be ready to deliver your best work.',
                'url' => 'https://moawen.sa',
                'button_title' => 'Dashboard',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
