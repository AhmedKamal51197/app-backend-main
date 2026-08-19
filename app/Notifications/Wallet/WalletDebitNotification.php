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
            ->subject('Wallet Transaction - Debit')
            ->view('team', [
                'title' => 'Wallet Debited',
                'description' => "A debit transaction of $" . number_format($this->amount, 2) . " has been processed from your Moawen wallet. This amount has been deducted from your available balance. Check your wallet for updated balance.",
                'url' => 'https://moawen.sa/wallet',
                'button_title' => 'View Wallet',
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
