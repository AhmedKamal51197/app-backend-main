<?php

namespace App\Notifications\Kyc;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * A class defined for KYC verification approved message
 */
class KycApprovedNotification extends Notification implements ShouldQueue
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
            ->subject('KYC Verification Approved')
            ->view('team', [
                'title' => 'KYC Verification Approved 🎉',
                'description' => 'Your KYC verification has been successfully approved. You can now access all features that require verified status. Thank you for completing the process!',
                'url' => 'www.moawen.sa',
                'button_title' => 'Go to Dashboard',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
