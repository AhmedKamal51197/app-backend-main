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
            ->subject('Payment Request Approved')
            ->view('team', [
                'title' => 'Payment Approved!',
                'description' => "Great news! Your payment request for $" . number_format($this->amount, 2) . " has been approved and processed. The funds should appear in your selected payment method within 1-2 business days.",
                'url' => 'https://moawen.sa/wallet',
                'button_title' => 'View Payment History',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'amount' => $this->amount,
        ];
    }
}
