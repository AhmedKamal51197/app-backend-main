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
            ->subject('Payment Request Submitted')
            ->view('team', [
                'title' => 'Payment Request Submitted',
                'description' => "Your payment request for $" . number_format($this->amount, 2) . " has been successfully submitted. We'll review your request and process it within 1-3 business days. You'll receive a notification once it's approved.",
                'url' => 'https://moawen.sa/wallet',
                'button_title' => 'View Payment Requests',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'amount' => $this->amount,
        ];
    }
}
