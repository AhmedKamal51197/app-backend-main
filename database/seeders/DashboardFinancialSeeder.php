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
 * Sample data for the admin financial-flow dashboard: pending withdrawal
 * (payment) requests with bank details, and pending refund requests, so the
 * admin can test approve/reject on them.
 *
 * Re-runnable: it tops the pending sample rows up to PENDING_TARGET, so after
 * approving/rejecting some you can run it again to get fresh pending ones.
 *
 *   php artisan db:seed --class="Database\Seeders\DashboardFinancialSeeder"
 */
class DashboardFinancialSeeder extends Seeder
{
    /**
     * Marker that identifies the rows created by this seeder.
     */
    private const SENTINEL = 'sample-seed';

    /**
     * How many pending sample rows to keep available for testing.
     */
    private const PENDING_TARGET = 5;

    public function run(): void
    {
        $this->topUpPendingWithdrawals();
        $this->topUpPendingRefunds();
    }

    /**
     * Ensure there are pending sample withdrawals (with bank details) to test.
     */
    private function topUpPendingWithdrawals(): void
    {
        $existing = PaymentRequest::where('notes', self::SENTINEL)
            ->where('status', PaymentRequestStatusEnum::PENDING->value)
            ->count();

        $need = self::PENDING_TARGET - $existing;

        if ($need <= 0) {
            return;
        }

        $users = User::query()->orderByDesc('id')->take($need)->get();

        if ($users->isEmpty()) {
            return;
        }

        $countryId = Country::query()->value('id');

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
                'status' => PaymentRequestStatusEnum::PENDING->value,
            ]);
        }
    }

    /**
     * Ensure there are pending sample refund requests (on existing orders) to test.
     */
    private function topUpPendingRefunds(): void
    {
        $existing = RefundRequest::where('reason', self::SENTINEL)
            ->where('status', RefundRequestStatusEnum::PENDING->value)
            ->count();

        $need = self::PENDING_TARGET - $existing;

        if ($need <= 0) {
            return;
        }

        $orders = Order::query()
            ->whereNotNull('seeker_id')
            ->orderByDesc('id')
            ->take($need)
            ->get();

        if ($orders->isEmpty()) {
            return;
        }

        foreach ($orders->values() as $i => $order) {
            RefundRequest::create([
                'amount' => (($i % 4) + 1) * 75,
                'refundable_type' => $order->getMorphClass(),
                'refundable_id' => $order->id,
                'reason' => self::SENTINEL,
                'status' => RefundRequestStatusEnum::PENDING->value,
            ]);
        }
    }
}
