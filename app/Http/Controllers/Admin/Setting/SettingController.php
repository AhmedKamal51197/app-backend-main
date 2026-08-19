<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Http\Requests\Admin\Setting\UpdateSettingRequest;
use App\Http\Requests\Admin\Setting\UpdateWithdrawalSettingsRequest;
use App\Http\Resources\Admin\Setting\SettingResource;
use App\Models\Setting;
use App\Services\Setting\SettingService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * A class defines the setting controller for admin
 */
class SettingController extends BaseAdminController
{
    /**
     * Load the service
     *
     * @param SettingService $service
     */
    public function __construct(protected SettingService $service)
    {
    }

    /**
     * List of settings
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $settings = $this->service->index($request->input('limit', Setting::PAGE_RESULT_LIMIT));

        return $this->jsonSuccess(SettingResource::collection($settings));
    }

    /**
     * Show setting
     *
     * @param Setting $setting
     *
     * @return JsonResponse
     */
    public function show(Setting $setting): JsonResponse
    {
        return $this->jsonSuccess(SettingResource::make($setting));
    }

    /**
     * Update setting
     *
     * @param UpdateSettingRequest $request
     * @param Setting $setting
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function update(UpdateSettingRequest $request, Setting $setting): JsonResponse
    {
        $setting = $this->service->update($setting, $request->validated());

        return $this->jsonSuccess(SettingResource::make($setting), __('Setting updated successfully'));
    }

    /**
     * Delete setting
     *
     * @param Setting $setting
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Setting $setting): JsonResponse
    {
        $this->service->delete($setting);

        return $this->jsonSuccess([], __('Setting deleted successfully'));
    }

    /**
     * Update withdrawal settings
     *
     * @param UpdateWithdrawalSettingsRequest $request
     *
     * @return JsonResponse
     */
    public function updateWithdrawalSettings(UpdateWithdrawalSettingsRequest $request): JsonResponse
    {
        $this->service->updateWithdrawalSettings($request->validated());

        return $this->jsonSuccess([], __('Withdrawal settings updated successfully'));
    }
}
