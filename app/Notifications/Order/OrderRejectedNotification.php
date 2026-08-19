<?php

namespace App\Notifications\Order;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * A class defined the order rejected notification
 */
class OrderRejectedNotification extends Notification
{
    use Queueable;

    protected string $code;
    protected string $name;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $code, string $name)
    {
        $this->code = $code;
        $this->name = $name;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param object $notifiable
     *
     * @return array
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $description = "We regret to inform you that your order " . $this->code . " for " . $this->name . " has been rejected.";
        $description .= " Please review the details on the order page or contact support for assistance.";

        return (new MailMessage)
            ->subject('Your Order Has Been Rejected')
            ->view('team', [
                'title' => 'Order Rejected',
                'description' => $description,
                'url' => "https://moawen.sa/en/order-history/",
                'button_title' => 'View Order',
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param object $notifiable
     *
     * @return array
     */
    public function toArray(object $notifiable): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
        ];
    }
}
