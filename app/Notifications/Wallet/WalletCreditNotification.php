<?php

namespace App\Notifications\Wallet;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * A class defined for wallet credit transaction message
 */
class WalletCreditNotification extends Notification implements ShouldQueue
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
            ->subject('معاملة محفظة - إيداع')
            ->view('team', [
                'title' => 'تم إيداع مبلغ في المحفظة',
                'description' => "خبر رائع! تم إيداع مبلغ في محفظتك في معاون قدره $" . number_format($this->amount, 2) . ". تمت إضافة هذا المبلغ إلى رصيدك المتاح وأصبح جاهزًا للاستخدام في معاملاتك.",
                'url' => 'https://moawen.sa/wallet',
                'button_title' => 'عرض المحفظة',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'amount' => $this->amount,
            'transaction_type' => 'credit',
        ];
    }
}
