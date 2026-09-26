<?php

namespace App\Notifications\Account;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * A class defined for bank account added successfully message
 */
class BankAccountAddedNotification extends Notification implements ShouldQueue
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
            ->subject('تمت إضافة الحساب البنكي بنجاح')
            ->view('team', [
                'title' => 'تمت إضافة الحساب البنكي',
                'description' => 'خبر رائع! تمت إضافة حسابك البنكي بنجاح إلى ملفك الشخصي في معاون. يمكنك الآن استلام المدفوعات مباشرة إلى حسابك البنكي. جميع المعاملات آمنة ومحمية.',
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
