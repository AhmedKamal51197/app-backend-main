<?php

namespace App\Notifications\Kyc;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * A class defined for KYC verification rejected message
 */
class KycRejectedNotification extends Notification implements ShouldQueue
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
            ->subject('KYC Verification Rejected')
            ->view('team', [
                'title' => 'KYC Verification Rejected',
                'description' => 'Unfortunately, your KYC verification was rejected. Please review your submitted documents and try again. If you need help, feel free to contact support.',
                'url' => 'https://api.whatsapp.com/send/?phone=%2B96566445995&text=I+need+help+with+my+KYC+verification&type=phone_number&app_absent=0',
                'button_title' => 'Contact Support',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
