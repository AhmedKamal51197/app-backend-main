<?php

namespace App\Notifications\Wallet;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * A class defined for wallet debit transaction message
 */
class WalletDebitNotification extends Notification implements ShouldQueue
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
            ->subject('معاملة محفظة - خصم')
            ->view('team', [
                'title' => 'تم خصم مبلغ من المحفظة',
                'description' => "تمت معالجة عملية خصم بمبلغ $" . number_format($this->amount, 2) . " من محفظتك في معاون. تم خصم هذا المبلغ من رصيدك المتاح. راجع محفظتك لمعرفة الرصيد المحدّث.",
                'url' => 'https://moawen.sa/wallet',
                'button_title' => 'عرض المحفظة',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'amount' => $this->amount,
            'transaction_type' => 'debit',
        ];
    }
}
