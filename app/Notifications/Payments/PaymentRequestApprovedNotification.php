<?php

namespace App\Notifications\Payments;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * A class defined for payment request approved message
 */
class PaymentRequestApprovedNotification extends Notification implements ShouldQueue
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
            ->subject('تمت الموافقة على طلب الدفع')
            ->view('team', [
                'title' => 'تمت الموافقة على الدفع!',
                'description' => "خبر رائع! تمت الموافقة على طلب الدفع الخاص بك بمبلغ $" . number_format($this->amount, 2) . " ومعالجته. من المفترض أن تظهر الأموال في طريقة الدفع التي اخترتها خلال 1-2 يوم عمل.",
                'url' => 'https://moawen.sa/wallet',
                'button_title' => 'عرض سجل المدفوعات',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'amount' => $this->amount,
        ];
    }
}
