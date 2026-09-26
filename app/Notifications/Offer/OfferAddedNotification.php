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
            ->subject('تم إرسال العرض بنجاح')
            ->view('team', [
                'title' => 'تم إرسال العرض',
                'description' => 'خبر رائع! تم إرسال عرضك بنجاح إلى العميل. ترقّب ردّه وكن مستعدًا لتقديم أفضل ما لديك.',
                'url' => 'https://moawen.sa',
                'button_title' => 'لوحة التحكم',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
