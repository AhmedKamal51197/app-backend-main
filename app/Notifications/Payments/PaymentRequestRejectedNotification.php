<?php

namespace App\Notifications\Payments;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * A class defined for payment request rejected message
 */
class PaymentRequestRejectedNotification extends Notification implements ShouldQueue
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
            ->subject('تم رفض طلب الدفع')
            ->view('team', [
                'title' => 'تحديث بشأن طلب الدفع',
                'description' => "يؤسفنا إبلاغك بأنه تم رفض طلب الدفع الخاص بك بمبلغ $" . number_format($this->amount, 2) . " . يرجى مراجعة المتطلبات وإعادة التقديم إذا لزم الأمر. تواصل مع الدعم لمزيد من التفاصيل.",
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
