<?php

namespace App\Console\Commands;

use App\Enums\DashboardNotificationTypeEnum;
use App\Models\DashboardNotification;
use Illuminate\Console\Command;

class SeedDashboardNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:seed-dashboard';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed 14 dashboard notifications for testing';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $notifications = [
            [
                'title' => 'New Order #12345',
                'details' => 'A new order has been placed by Seeker John Doe.',
                'type' => DashboardNotificationTypeEnum::NORMAL->value,
                'resolved' => false,
                'is_seen' => false,
            ],
            [
                'title' => 'Dispute on Order #54321',
                'details' => 'Provider Jane Smith has opened a dispute regarding payment.',
                'type' => DashboardNotificationTypeEnum::DISPUTE->value,
                'resolved' => false,
                'is_seen' => false,
            ],
            [
                'title' => 'System Maintenance',
                'details' => 'Scheduled maintenance in 2 hours.',
                'type' => DashboardNotificationTypeEnum::NORMAL->value,
                'resolved' => true,
                'is_seen' => true,
            ],
            [
                'title' => 'New Freelancer Application',
                'details' => 'Mostaql Ahmed has applied to become a provider.',
                'type' => DashboardNotificationTypeEnum::NORMAL->value,
                'resolved' => false,
                'is_seen' => false,
            ],
            [
                'title' => 'Withdrawal Request #101',
                'details' => 'Seeker Mary has requested a withdrawal of $500.',
                'type' => DashboardNotificationTypeEnum::NORMAL->value,
                'resolved' => false,
                'is_seen' => false,
            ],
            [
                'title' => 'Report Received',
                'details' => 'A user has been reported for inappropriate behavior.',
                'type' => DashboardNotificationTypeEnum::DISPUTE->value,
                'resolved' => false,
                'is_seen' => false,
            ],
            [
                'title' => 'Service Approved',
                'details' => 'Service "Logo Design" has been approved by admin.',
                'type' => DashboardNotificationTypeEnum::NORMAL->value,
                'resolved' => true,
                'is_seen' => true,
            ],
            [
                'title' => 'KYC Verification Pending',
                'details' => 'User Khalid has uploaded KYC documents for review.',
                'type' => DashboardNotificationTypeEnum::NORMAL->value,
                'resolved' => false,
                'is_seen' => false,
            ],
            [
                'title' => 'Payment Failed',
                'details' => 'Transaction #TRX789 failed due to insufficient funds.',
                'type' => DashboardNotificationTypeEnum::NORMAL->value,
                'resolved' => false,
                'is_seen' => false,
            ],
            [
                'title' => 'Dispute Resolved #222',
                'details' => 'The dispute between Ali and Sara has been closed.',
                'type' => DashboardNotificationTypeEnum::DISPUTE->value,
                'resolved' => true,
                'is_seen' => true,
            ],
            [
                'title' => 'New Support Ticket',
                'details' => 'User needs help with account recovery.',
                'type' => DashboardNotificationTypeEnum::NORMAL->value,
                'resolved' => false,
                'is_seen' => false,
            ],
            [
                'title' => 'Subscription Expired',
                'details' => 'Mostaql Premium subscription for User #55 expired.',
                'type' => DashboardNotificationTypeEnum::NORMAL->value,
                'resolved' => false,
                'is_seen' => true,
            ],
            [
                'title' => 'Large Transaction Warning',
                'details' => 'A transaction of $5000 was detected.',
                'type' => DashboardNotificationTypeEnum::NORMAL->value,
                'resolved' => false,
                'is_seen' => false,
            ],
            [
                'title' => 'New Project Posted',
                'details' => '"Mobile App Development" project is live.',
                'type' => DashboardNotificationTypeEnum::NORMAL->value,
                'resolved' => false,
                'is_seen' => false,
            ],
        ];

        foreach ($notifications as $notification) {
            DashboardNotification::create($notification);
        }

        $this->info('14 dashboard notifications seeded successfully!');

        return 0;
    }
}
