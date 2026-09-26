<?php

namespace App\Notifications\Payments;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * A class defined for payment request added message
 */
class PaymentRequestAddedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected float $amount;

    public function __construct(float $amount)
    {
        $this->amount = $amount;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('تم تقديم طلب الدفع')
            ->view('team', [
                'title' => 'تم تقديم طلب الدفع',
                'description' => "تم تقديم طلب الدفع الخاص بك بمبلغ $" . number_format($this->amount, 2) . " بنجاح. سنراجع طلبك ونعالجه خلال 1-3 أيام عمل. ستصلك رسالة إشعار بمجرد الموافقة عليه.",
                'url' => 'https://moawen.sa/wallet',
                'button_title' => 'عرض طلبات الدفع',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'amount' => $this->amount,
        ];
    }
}
