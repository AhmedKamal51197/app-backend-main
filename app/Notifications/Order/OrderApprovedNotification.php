<?php

namespace App\Notifications\Order;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * A class defined the order approved
 */
class OrderApprovedNotification extends Notification
{
    use Queueable;

    protected string $code;
    protected string $name;

    /**
     * Create a new notification instance.
     */
    public function __construct($code, $name)
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
            ->subject('تمت الموافقة على طلبك!')
            ->view('team', [
                'title' => 'تمت الموافقة على الطلب!',
                'description' => "خبر رائع! طلبك " . $this->code . " لـ" . $this->name . " تمت الموافقة عليه وأصبح نشطًا الآن. يمكنك متابعة تقدّمه والتواصل مع المستقل من صفحة طلبك.",
                'url' => "https://moawen.sa/en/order-history",
                'button_title' => 'عرض تفاصيل الطلب',
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
