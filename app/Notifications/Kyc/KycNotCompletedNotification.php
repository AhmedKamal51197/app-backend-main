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
            ->subject('أكمل عملية التحقق من هويتك (KYC)')
            ->view('team', [
                'title' => 'أكمل عملية التحقق من هويتك (KYC) 🛡️',
                'description' => 'لاحظنا أنك لم تُكمل عملية التحقق من هويتك (KYC) بعد. يرجى إكمال العملية لفتح جميع ميزات حسابك.',
                'url' => 'https://www.moawen.sa/kyc', // or route('kyc.form')
                'button_title' => 'أكمل التحقق الآن',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
