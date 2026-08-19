<?php

namespace App\Services\CommissionSettings;

use App\Events\LogExceptionEvent;
use App\Models\CommissionSetting;
use App\Models\Setting;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * A class defines the commission settings service
 */
class CommissionSettingsService
{
    /**
     * Index the commissions settings
     *
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function index(int $perPage = Setting::PAGE_RESULT_LIMIT): LengthAwarePaginator
    {
        return CommissionSetting::query()
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Update commission setting
     *
     * @param CommissionSetting $commissionSetting
     * @param array $data
     *
     * @return CommissionSetting
     *
     * @throws Exception
     */
    public function update(CommissionSetting $commissionSetting, array $data): CommissionSetting
    {
        try {
            DB::beginTransaction();

            $commissionSetting->update($data);

            DB::commit();

            return $commissionSetting->fresh();

        } catch (\Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Delete commission setting
     *
     * @param CommissionSetting $commissionSetting
     *
     * @return bool
     *
     * @throws Exception
     */
    public function delete(CommissionSetting $commissionSetting): bool
    {
        try {
            DB::beginTransaction();

            $commissionSetting->delete();

            DB::commit();

            return true;

        } catch (\Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }
}
