<?php

namespace App\Http\Controllers\Api\Page;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Page\PageResource;
use App\Models\Page;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * A class defines the page controller for users
 */
class PageController extends Controller
{
    use ApiResponse;

    /**
     * Get page by key
     *
     * @param Page $page
     *
     * @return JsonResponse
     */
    public function show(Page $page): JsonResponse
    {
        if (!$page->is_active) {
            return $this->jsonError(__('Page not found'), 404);
        }

        return $this->jsonSuccess(PageResource::make($page));
    }
}
