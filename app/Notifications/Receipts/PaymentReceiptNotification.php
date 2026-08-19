<?php

namespace App\Notifications\Receipts;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * A class defined for the payment receipt notification
 */
class PaymentReceiptNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $name,
        public string $amount,
        public string $date,
    )
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Payment Receipt')
            ->view('receipt', [
                'title' => 'Payment Receipt 🎉',
                'name' => $this->name,
                'amount' => $this->amount,
                'date' => $this->date,
                'url' => 'https://www.moawen.sa/',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'amount' => $this->amount,
            'date' => $this->date,
        ];
    }
}
