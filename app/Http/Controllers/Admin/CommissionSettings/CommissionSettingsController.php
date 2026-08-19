<?php

namespace App\Http\Controllers\Admin\CommissionSettings;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Http\Requests\Api\Admin\CommissionSettings\UpdateCommissionSettingRequest;
use App\Http\Resources\Admin\CommissionSettings\CommissionSettingsResource;
use App\Models\CommissionSetting;
use App\Models\Setting;
use App\Services\CommissionSettings\CommissionSettingsService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * A class defines the commission settings controller
 */
class CommissionSettingsController extends BaseAdminController
{
    /**
     * Call the service
     *
     * @param CommissionSettingsService $service
     */
    public function __construct(protected CommissionSettingsService $service)
    {
    }

    /**
     * List of the system Commissions Settings
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return $this->jsonSuccess(CommissionSettingsResource::collection($this->service->index($request->input('limit', Setting::PAGE_RESULT_LIMIT))));
    }

    /**
     * Show Commission Settings
     *
     * @param CommissionSetting $commissionSetting
     *
     * @return JsonResponse
     */
    public function show(CommissionSetting $commissionSetting): JsonResponse
    {
        return $this->jsonSuccess(CommissionSettingsResource::make($commissionSetting));
    }

    /**
     * Update Commission Settings
     *
     * @param UpdateCommissionSettingRequest $request
     * @param CommissionSetting $commissionSetting
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function update(UpdateCommissionSettingRequest $request, CommissionSetting $commissionSetting): JsonResponse
    {
        $commissionSetting = $this->service->update($commissionSetting, $request->validated());

        return $this->jsonSuccess(
            CommissionSettingsResource::make($commissionSetting),
            __('Commission setting updated successfully')
        );
    }

    /**
     * Delete Commission Settings
     *
     * @param CommissionSetting $commissionSetting
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(CommissionSetting $commissionSetting): JsonResponse
    {
        $this->service->delete($commissionSetting);

        return $this->jsonSuccess(
            [],
            __('Commission setting deleted successfully')
        );
    }
}
