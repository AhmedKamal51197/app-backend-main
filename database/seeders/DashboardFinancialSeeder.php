<?php

namespace Database\Seeders;

use App\Enums\PaymentRequestStatusEnum;
use App\Enums\RefundRequestStatusEnum;
use App\Models\BankAccount;
use App\Models\Country;
use App\Models\Order;
use App\Models\PaymentRequest;
use App\Models\RefundRequest;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Sample data for the admin financial-flow dashboard: withdrawal (payment)
 * requests with the requester's bank details, and refund requests, so the
 * withdrawals and refunds tables are populated during development.
 *
 * Safe to run more than once: it skips when the sample rows already exist.
 *
 *   php artisan db:seed --class=Database\\Seeders\\DashboardFinancialSeeder
 */
class DashboardFinancialSeeder extends Seeder
{
    /**
     * Marker that identifies the rows created by this seeder.
     */
    private const SENTINEL = 'sample-seed';

    public function run(): void
    {
        $this->seedWithdrawals();
        $this->seedRefunds();
    }

    /**
     * Create sample withdrawal (payment) requests, making sure each requester
     * has bank details so the payout export is meaningful.
     */
    private function seedWithdrawals(): void
    {
        if (PaymentRequest::where('notes', self::SENTINEL)->exists()) {
            return;
        }

        $users = User::query()->orderByDesc('id')->take(8)->get();

        if ($users->isEmpty()) {
            return;
        }

        $countryId = Country::query()->value('id');
        $statuses = PaymentRequestStatusEnum::cases();

        foreach ($users->values() as $i => $user) {
            BankAccount::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'user_name' => $user->name ?: 'Account Holder',
                    'iban' => 'SA' . str_pad((string) (3000000000000000000 + $user->id), 22, '0', STR_PAD_LEFT),
                    'swift_code' => 'RJHISARI',
                    'bank_name' => 'Al Rajhi Bank',
                    'bank_address' => 'Riyadh',
                    'branch_name' => 'Main Branch',
                    'user_address' => 'Riyadh, KSA',
                    'country_id' => $countryId,
                ]
            );

            $amount = (($i % 5) + 1) * 100;
            $fee = 2;

            PaymentRequest::create([
                'amount' => $amount,
                'fee' => $fee,
                'net_amount' => max($amount - $fee, 0),
                'notes' => self::SENTINEL,
                'user_id' => $user->id,
                'status' => $statuses[$i % count($statuses)]->value,
                'created_at' => now()->subDays($i),
                'updated_at' => now()->subDays($i),
            ]);
        }
    }

    /**
     * Create sample refund requests on existing orders (the refund recipient is
     * the order's seeker), so the dashboard financial-flow refunds table has
     * data with statuses.
     */
    private function seedRefunds(): void
    {
        if (RefundRequest::where('reason', self::SENTINEL)->exists()) {
            return;
        }

        $orders = Order::query()
            ->whereNotNull('seeker_id')
            ->orderByDesc('id')
            ->take(6)
            ->get();

        if ($orders->isEmpty()) {
            return;
        }

        $statuses = RefundRequestStatusEnum::cases();

        foreach ($orders->values() as $i => $order) {
            RefundRequest::create([
                'amount' => (($i % 4) + 1) * 75,
                'refundable_type' => $order->getMorphClass(),
                'refundable_id' => $order->id,
                'reason' => self::SENTINEL,
                'status' => $statuses[$i % count($statuses)]->value,
                'created_at' => now()->subDays($i),
                'updated_at' => now()->subDays($i),
            ]);
        }
    }
}
