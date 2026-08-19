<?php

namespace App\Http\Controllers\Api\Banner;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\Api\Banner\BannerResource;
use App\Models\Banner;
use App\Models\Setting;
use App\Services\Banner\BannerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BannerController extends BaseApiController
{
    /**
     * Load the service in constructor
     */
    public function __construct(private readonly bannerService $service) {}

    /**
     * Get all banners
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $mainBanners = $this->service->index(
            $request->input('limit', Setting::PAGE_RESULT_LIMIT),
            $request->input('type', '')
        );

        return $this->jsonSuccess(BannerResource::collection($mainBanners));
    }


    /**
     * Show banner
     *
     * @param Banner $banner
     *
     * @return JsonResponse
     */
    public function show(Banner $banner): JsonResponse
    {
        return $this->jsonSuccess(BannerResource::make($banner->load('attachment')));
    }
}
