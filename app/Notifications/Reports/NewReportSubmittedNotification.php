<?php

namespace App\Notifications\Reports;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * A class defined for new report submitted message
 */
class NewReportSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Report Submitted Successfully')
            ->view('team', [
                'title' => 'Report Received',
                'description' => 'Thank you for submitting your report. We take all reports seriously and will investigate the matter promptly. Our team will review the details and take appropriate action if necessary.',
                'url' => 'https://moawen.sa',
                'button_title' => 'Contact Support',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
