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
            ->subject('Wallet Transaction - Credit')
            ->view('team', [
                'title' => 'Wallet Credited',
                'description' => "Great news! Your Moawen wallet has been credited with $" . number_format($this->amount, 2) . ". This amount has been added to your available balance and is ready to use for your transactions.",
                'url' => 'https://moawen.sa/wallet',
                'button_title' => 'View Wallet',
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
