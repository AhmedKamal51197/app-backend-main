<?php

namespace App\Http\Controllers\Admin\Banner;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Admin\Banner\StoreBannerRequest;
use App\Http\Requests\Admin\Banner\UpdateBannerRequest;
use App\Http\Resources\Admin\Banner\BannerResource;
use App\Models\Banner;
use App\Models\Setting;
use App\Services\Banner\BannerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BannerController extends BaseApiController
{
    public function __construct(private BannerService $service) {}

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
     * Store new banner
     *
     * @param StoreBannerRequest $request
     *
     * @return JsonResponse
     *
     * @throws \Exception
     */
    public function store(StoreBannerRequest $request): JsonResponse
    {
        $banner = $this->service->create($request->validated());

        return $this->jsonSuccess(BannerResource::make($banner), (__('Banner created successfully')));
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

    /**
     * Update banner
     *
     * @param UpdateBannerRequest $request
     *
     * @param Banner $banner
     */
    public function update(UpdateBannerRequest $request, Banner $banner): JsonResponse
    {
        $banner = $this->service->update($banner, $request->validated());

        return $this->jsonSuccess(BannerResource::make($banner), (__('Banner updated successfully')));


    }

    /**
     * Delete banner
     *
     * @param Banner $banner
     *
     * @return JsonResponse
     */
    public function destroy(Banner $banner): JsonResponse
    {
        $this->service->delete($banner);

        return $this->jsonSuccess([], 'Banner deleted successfully');
    }

    /**
     * Toggle banner active status
     *
     * @param Banner $banner
     *
     * @return JsonResponse
     */
    public function toggleActive(Banner $banner): JsonResponse
    {
        $banner = $this->service->toggleActive($banner);
        return $this->jsonSuccess(BannerResource::make($banner), __('Banner active status toggled successfully'));
    }

}
