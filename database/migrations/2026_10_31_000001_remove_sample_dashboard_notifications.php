<?php

use App\Models\DashboardNotification;
use Illuminate\Database\Migrations\Migration;

/**
 * Remove the English sample notifications seeded during development so the
 * dashboard only shows real events. Soft delete, so they can be restored.
 */
return new class extends Migration
{
    public function up(): void
    {
        DashboardNotification::whereIn('title', [
            'New Order #12345',
            'Dispute on Order #54321',
            'System Maintenance',
            'New Freelancer Application',
            'Withdrawal Request #101',
            'Report Received',
            'Service Approved',
            'KYC Verification Pending',
            'Payment Failed',
            'Dispute Resolved #222',
            'New Support Ticket',
            'Subscription Expired',
            'Large Transaction Warning',
            'New Project Posted',
        ])->delete();
    }

    public function down(): void
    {
        DashboardNotification::onlyTrashed()->restore();
    }
};
