<?php

namespace App\Services\Home;

use App\Enums\KycStatusEnum;
use App\Enums\ServiceTypeEnum;
use App\Enums\UserStatusEnum;
use App\Models\Role;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * A class defines the home service
 */
class HomeService
{
    /**
     * Get all services
     *
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function oneTimeServices(int $perPage = Setting::PAGE_RESULT_LIMIT): LengthAwarePaginator
    {
        return Service::query()
            ->with(['attachments', 'category', 'subCategory', 'skills', 'skills.skill', 'packages', 'user'])
            ->whereHas('user', function ($query) {
                $query->where('status', UserStatusEnum::ACTIVE->value)
                    ->whereHas('kycs', function ($query) {
                        $query->where('status', KycStatusEnum::APPROVED->value);
                    });
            })
            ->where('hidden', '=', false)
            ->where('is_approved', '=', true)
            ->where('type', '=', ServiceTypeEnum::ONE_TIME->value)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Get all part-time services
     *
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function partTimeServices(int $perPage = Setting::PAGE_RESULT_LIMIT): LengthAwarePaginator
    {
        return Service::query()
            ->with(['attachments', 'category', 'subCategory', 'skills', 'skills.skill', 'packages', 'user'])
            ->where('type', '=', ServiceTypeEnum::PART_TIME->value)
            ->whereHas('user', function ($query) {
                $query->where('status', UserStatusEnum::ACTIVE->value)
                    ->whereHas('kycs', function ($query) {
                        $query->where('status', KycStatusEnum::APPROVED->value);
                    });
            })
            ->where('hidden', '=', false)
            ->where('is_approved', '=', true)
            ->where('custom_offer', '=', false)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Index users
     *
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function users(int $perPage = Setting::PAGE_RESULT_LIMIT): LengthAwarePaginator
    {
        $providerRoleId = Role::where('name', 'provider')->value('id');

        return User::query()
            ->whereHas('roles', function ($query) use ($providerRoleId) {
                $query->where('id', $providerRoleId);
            })
            ->where('status', '=', UserStatusEnum::ACTIVE->value)
            ->with('category')
            ->whereHas('kycs', function ($query) {
                $query->where('status', KycStatusEnum::APPROVED->value);
            })
            ->paginate($perPage);
    }
}



