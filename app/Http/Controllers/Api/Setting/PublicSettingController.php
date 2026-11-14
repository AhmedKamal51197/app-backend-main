<?php

namespace App\Http\Controllers\Api\Setting;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\Setting\PublicSettingService;
use Illuminate\Http\JsonResponse;

/**
 * Public settings for the website (contact details, colors, social links).
 */
class PublicSettingController extends BaseApiController
{
    public function __construct(private readonly PublicSettingService $service)
    {
    }

    public function show(): JsonResponse
    {
        return $this->jsonSuccessWithStrings($this->service->get());
    }
}
