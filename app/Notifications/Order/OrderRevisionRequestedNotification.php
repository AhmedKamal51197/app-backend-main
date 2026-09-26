<?php

namespace App\Notifications\Order;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * A class defined the order revision requested notification
 */
class OrderRevisionRequestedNotification extends Notification
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
        return (new MailMessage)
            ->subject('تم طلب تعديل على طلبك')
            ->view('team', [
                'title' => 'تم طلب تعديل!',
                'description' => "تم طلب تعديل " . " على طلبك " . $this->code . " (" . $this->name . "). يرجى مراجعة الملاحظات وإجراء التعديلات اللازمة.",
                'url' => "https://moawen.sa/en/order-history/",
                'button_title' => 'عرض تفاصيل التعديل',
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
