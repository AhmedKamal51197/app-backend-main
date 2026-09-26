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
            ->subject('تم رفض التحقق من الهوية (KYC)')
            ->view('team', [
                'title' => 'تم رفض التحقق من الهوية (KYC)',
                'description' => 'للأسف، تم رفض التحقق من هويتك. يرجى مراجعة المستندات التي قدمتها والمحاولة مرة أخرى. إذا احتجت إلى مساعدة، لا تتردد في التواصل مع الدعم.',
                'url' => 'https://api.whatsapp.com/send/?phone=%2B96566445995&text=I+need+help+with+my+KYC+verification&type=phone_number&app_absent=0',
                'button_title' => 'تواصل مع الدعم',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
