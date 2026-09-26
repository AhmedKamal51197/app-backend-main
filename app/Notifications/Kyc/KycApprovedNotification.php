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
            ->subject('تمت الموافقة على التحقق من الهوية (KYC)')
            ->view('team', [
                'title' => 'تمت الموافقة على التحقق من الهوية (KYC) 🎉',
                'description' => 'تمت الموافقة على التحقق من هويتك بنجاح. يمكنك الآن الوصول إلى جميع الميزات التي تتطلب حالة موثّقة. شكرًا لإكمالك العملية!',
                'url' => 'www.moawen.sa',
                'button_title' => 'الانتقال إلى لوحة التحكم',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
