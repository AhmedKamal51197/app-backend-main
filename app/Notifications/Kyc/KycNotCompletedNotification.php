<?php

namespace App\Notifications\Kyc;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * A class defined for notifying user to complete KYC
 */
class KycNotCompletedNotification extends Notification implements ShouldQueue
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
            ->subject('Complete Your KYC Verification')
            ->view('team', [
                'title' => 'Complete Your KYC Verification 🛡️',
                'description' => 'We noticed you haven’t completed your KYC verification yet. Please complete the process to unlock all features of your account.',
                'url' => 'https://www.moawen.sa/kyc', // or route('kyc.form')
                'button_title' => 'Complete KYC Now',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
