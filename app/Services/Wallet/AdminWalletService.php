<?php

namespace App\Services\Wallet;

use App\Models\PaymentRequest;
use App\Models\Setting;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * A class defines the Admin Wallet service
 */
class AdminWalletService
{
    /**
     * Get wallet analytics, user wallets, and financial flow
     *
     * @param array $data
     *
     * @return array
     */
    public function index(array $data): array
    {
        return [
            'analytics' => $this->getAnalytics(),
            'user_wallets' => $this->getUserWallets($data['user_wallets_limit'], $data['user_wallets_page'], $data['user_role'] ?? 'seeker'),
            'deposits' => $this->getDeposits($data['deposits_limit'], $data['deposits_page']),
            'withdrawals' => $this->getWithdrawals($data['withdrawals_limit'], $data['withdrawals_page'], $data['withdrawal_status']),
        ];
    }

    /**
     * Get wallet analytics
     *
     * @return array
     */
    public function getAnalytics(): array
    {
        $seekersBalance = Wallet::whereHas('user', function ($query) {
            $query->role('seeker');
        })->sum('balance');

        $providersBalance = Wallet::whereHas('user', function ($query) {
            $query->role('provider');
        })->sum('balance');

        // Total Withdrawals
        $totalWithdrawals = PaymentRequest::where('status', 'approved')
            ->sum('amount');

        // Pending Withdrawals
        $pendingWithdrawals = PaymentRequest::where('status', 'pending')
            ->sum('amount');

        return [
            'seekers_balance' => round($seekersBalance, 2),
            'providers_balance' => round($providersBalance, 2),
            'total_withdrawals' => round($totalWithdrawals, 2),
            'pending_withdrawals' => round($pendingWithdrawals, 2),
        ];
    }

    /**
     * Get user wallets with balances
     *
     * @param int $perPage
     * @param int $page
     * @param string $role
     *
     * @return LengthAwarePaginator
     */
    public function getUserWallets(int $perPage = Setting::PAGE_RESULT_LIMIT, int $page = 1, string $role = 'seeker')
    {
        $users = User::whereHas('roles', function ($query) use ($role) {
                $query->where('name', $role);
            })
            ->orderByDesc('created_at')
            ->paginate($perPage, ['*'], 'page', $page);

        $users->getCollection()->transform(function ($user) {
            $user->balance = $user->walletBalance();

            $user->withdraw_balance = $user->paymentRequests()
                ->where('status', 'approved')
                ->sum('amount');

            $user->pending_balance = $user->pendingBalance();

            $user->spent_balance = $user->totalSpending();

            return $user;
        });

        return $users;
    }

    /**
     * Get deposits (wallet credits)
     *
     * @param int $perPage
     * @param int $page
     *
     * @return LengthAwarePaginator
     */
    public function getDeposits(int $perPage = Setting::PAGE_RESULT_LIMIT, int $page = 1)
    {
        $deposits = Wallet::where('credit', '>', 0)
            ->with(['user', 'referencable'])
            ->orderByDesc('created_at')
            ->paginate($perPage, ['*'], 'page', $page);

        // Calculate balance before and after for each deposit
        $deposits->getCollection()->transform(function ($wallet) {
            $balanceBefore = Wallet::where('user_id', $wallet->user_id)
                ->where('created_at', '<', $wallet->created_at)
                ->selectRaw('balance')
                ->value('balance') ?? 0;

            $wallet->balance_before = round($balanceBefore, 2);
            $wallet->balance_after = round($balanceBefore + $wallet->credit, 2);

            return $wallet;
        });

        return $deposits;
    }

    /**
     * Get withdrawals (payment requests)
     *
     * @param int $perPage
     * @param int $page
     * @param string|null $status
     *
     * @return LengthAwarePaginator
     */
    public function getWithdrawals(int $perPage = Setting::PAGE_RESULT_LIMIT, int $page = 1, ?string $status = null)
    {
        $query = PaymentRequest::with(['user'])
            ->orderByDesc('created_at');

        if (!empty($status)) {
            $query->where('status', $status);
        }

        $withdrawals = $query->paginate($perPage, ['*'], 'page', $page);

        $withdrawals->getCollection()->transform(function ($request) {
            $request->current_balance = $request->user->walletBalance();

            $withdrawalMethods = [];
            if ($request->user->hasBankAccount()) {
                $withdrawalMethods[] = 'bank_transfer';
            }
            if ($request->user->hasPaypal()) {
                $withdrawalMethods[] = 'paypal';
            }

            $request->withdrawal_methods = $withdrawalMethods;
            return $request;
        });

        return $withdrawals;
    }
}

