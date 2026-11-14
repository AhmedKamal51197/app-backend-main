<?php

namespace App\Http\Controllers\Api\Page;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Page\PublicPageResource;
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

        $page->load('sections');
        $lang = PublicPageResource::languageFor(app()->getLocale(), $page->en_enabled);

        return $this->jsonSuccessWithStrings(PublicPageResource::make($page)->resolve())
            ->header('Content-Language', $lang);
    }
}
