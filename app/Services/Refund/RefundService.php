<?php

namespace App\Services\Refund;

use App\Models\Refund;
use App\Models\Setting;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * A class defines the refund service
 */
class RefundService
{
    /**
     * Index the commissions
     *
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function index(int $perPage = Setting::PAGE_RESULT_LIMIT): LengthAwarePaginator
    {
        return Refund::query()
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }
}
