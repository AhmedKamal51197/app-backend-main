<?php

namespace App\Notifications\Account;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * A class defined for PayPal added successfully message
 */
class PayPalAddedNotification extends Notification implements ShouldQueue
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
            ->subject('تمت إضافة حساب PayPal بنجاح')
            ->view('team', [
                'title' => 'تم ربط حساب PayPal',
                'description' => 'ممتاز! تم ربط حساب PayPal الخاص بك بنجاح بملفك الشخصي في معاون. يمكنك الآن استلام المدفوعات عبر PayPal بكل سهولة وأمان.',
                'url' => 'https://moawen.sa/wallet',
                'button_title' => 'عرض طرق الدفع',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
