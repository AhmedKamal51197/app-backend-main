<?php

namespace App\Http\Controllers\Admin\Portfolio;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Http\Resources\Api\Portfolio\PortfolioResource;
use App\Models\Portfolio;
use App\Models\Setting;
use App\Services\Portfolio\AdminPortfolioService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * A class defines the Admin Portfolio controller
 */
class PortfolioController extends BaseAdminController
{
    /**
     * Call the service
     */
    public function __construct(protected AdminPortfolioService $adminPortfolioService) {}

    /**
     * List of the system portfolios
     */
    public function index(Request $request): JsonResponse
    {
        $portfolios = $this->adminPortfolioService->index(
            $request->input('search', ''),
            $request->input('limit', Setting::PAGE_RESULT_LIMIT),
            $request->input('page', Setting::PAGE),
            $request->input('hidden', ''),
            $request->input('order_by', 'latest')
        );

        return $this->jsonSuccess(PortfolioResource::collection($portfolios));
    }

    /**
     * Show portfolio
     */
    public function show(Portfolio $portfolio): JsonResponse
    {
        $portfolio->loadCount('favorites')
            ->load(['user', 'category', 'subCategory', 'attachments', 'skills', 'skills.skill']);

        return $this->jsonSuccess(PortfolioResource::make($portfolio));
    }

    /**
     * Toggle portfolio hidden status
     */
    public function toggleHidden(Portfolio $portfolio): JsonResponse
    {
        $portfolio = $this->adminPortfolioService->toggleHidden($portfolio);

        return $this->jsonSuccess(PortfolioResource::make($portfolio), __('Portfolio status toggled successfully'));
    }

    /**
     * Delete portfolio
     *
     * @param Portfolio $portfolio
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function delete(Portfolio $portfolio): JsonResponse
    {
        $this->adminPortfolioService->delete($portfolio);

        return $this->jsonSuccess([], __('Portfolio deleted successfully'));
    }
}
