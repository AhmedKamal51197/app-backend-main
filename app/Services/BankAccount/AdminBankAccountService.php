<?php

namespace App\Services\BankAccount;

use App\Models\BankAccount;
use App\Models\PaymentRequest;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Admin-side access to users' bank accounts: listing and exporting, including
 * the bank details of the users who requested a withdrawal so the admin can
 * perform the manual bank transfers.
 */
class AdminBankAccountService
{
    /**
     * Paginated list of all bank accounts with their owner.
     *
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function index(int $perPage = Setting::PAGE_RESULT_LIMIT): LengthAwarePaginator
    {
        return BankAccount::query()
            ->with(['user', 'country'])
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Every bank account (for a full export).
     *
     * @return Collection
     */
    public function allBankAccounts(): Collection
    {
        return BankAccount::query()
            ->with(['user', 'country'])
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Withdrawal (payment) requests with the requester's bank details, so the
     * admin can export a payout sheet. Filtered by status when provided, and
     * narrowed to specific requests (by uuid) when a selection is passed, so
     * the dashboard can export only the rows the admin ticked, or a single one.
     *
     * @param string|null $status
     * @param array<int, string>|null $ids
     *
     * @return Collection
     */
    public function withdrawalRequests(?string $status = null, ?array $ids = null): Collection
    {
        return PaymentRequest::query()
            ->with(['user.bankAccount.country'])
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($ids, fn ($query) => $query->whereIn('uuid', $ids))
            ->orderByDesc('created_at')
            ->get();
    }
}
