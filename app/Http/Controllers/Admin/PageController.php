<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Page\SavePageRequest;
use App\Http\Requests\Admin\Page\UpdatePageRequest;
use App\Http\Resources\Admin\Page\PageResource;
use App\Models\Page;
use App\Models\Setting;
use App\Services\Page\PageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * A class defines the page controller for admin
 */
class PageController extends BaseAdminController
{
    /**
     * Call the service
     *
     * @param PageService $service
     */
    public function __construct(protected PageService $service)
    {
    }

    /**
     * List of pages
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $pages = $this->service->index($request->input('limit', Setting::PAGE_RESULT_LIMIT));

        return $this->jsonSuccess(PageResource::collection($pages));
    }

    /**
     * Show page
     *
     * @param Page $page
     *
     * @return JsonResponse
     */
    public function show(Page $page): JsonResponse
    {
        return $this->jsonSuccessWithStrings(PageResource::make($page->load('sections'))->resolve());
    }

    /**
     * Save the page with all its sections (both languages) in one go.
     *
     * @param SavePageRequest $request
     * @param Page $page
     *
     * @return JsonResponse
     */
    public function save(SavePageRequest $request, Page $page): JsonResponse
    {
        $page = $this->service->save($page, $request->validated());

        // Versions like "1.0" and slugs must stay strings (no numeric conversion).
        return $this->jsonSuccessWithStrings(PageResource::make($page)->resolve(), __('Page updated successfully'));
    }

    /**
     * Update page
     *
     * @param UpdatePageRequest $request
     * @param Page $page
     *
     * @return JsonResponse
     */
    public function update(UpdatePageRequest $request, Page $page): JsonResponse
    {
        $page = $this->service->update($page, $request->validated());

        return $this->jsonSuccess(PageResource::make($page), __('Page updated successfully')
        );
    }
}
