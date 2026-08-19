<?php

namespace App\Http\Controllers\Api\Portfolio;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\Portfolio\AddPortfolioAttachmentRequest;
use App\Http\Requests\Api\Portfolio\AddPortfolioSkillsRequest;
use App\Http\Requests\Api\Portfolio\EditPortfolioRequest;
use App\Http\Requests\Api\Portfolio\RemovePortfolioSkillsRequest;
use App\Http\Requests\Api\Portfolio\StorePortfolioRequest;
use App\Http\Resources\Api\Portfolio\PortfolioResource;
use App\Models\Attachment;
use App\Models\Portfolio;
use App\Models\Setting;
use App\Models\User;
use App\Services\Portfolio\PortfolioService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * A class defines the user portfolio controller actions
 */
class PortfolioController extends BaseApiController
{
    /**
     * Define constructor to get the service
     */
    public function __construct(protected PortfolioService $service)
    {
    }

    /**
     * List of the user portfolios
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $portfolios = $this->service->index(
            $request->input('limit', Setting::PAGE_RESULT_LIMIT),
            $request->input('page', Setting::PAGE),
            $request->user()->id,
            true,
        );

        return $this->jsonSuccess(PortfolioResource::collection($portfolios));
    }

    /**
     * List of the user portfolios
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function seekerIndex(User $user, Request $request): JsonResponse
    {
        $portfolios = $this->service->index(
            $request->input('limit', Setting::PAGE_RESULT_LIMIT),
            $request->input('page', Setting::PAGE),
            $user->id,
        );

        return $this->jsonSuccess(PortfolioResource::collection($portfolios));
    }

    /**
     * Show the portfolio
     *
     * @param Portfolio $portfolio
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function show(Portfolio $portfolio,  Request $request): JsonResponse
    {
        $portfolio = $this->service->show($portfolio, $request->user());

        return $this->jsonSuccess(PortfolioResource::make($portfolio));
    }

    /**
     * Store new portfolio
     *
     * @param StorePortfolioRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function store(StorePortfolioRequest $request): JsonResponse
    {
        $portfolio = $this->service->store(request()->user(), $request->validated());

        return $this->jsonSuccess(PortfolioResource::make($portfolio));
    }

    /**
     * Edit Portfolio
     *
     * @param EditPortfolioRequest $request
     * @param Portfolio $portfolio
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function edit(EditPortfolioRequest $request, Portfolio $portfolio): JsonResponse
    {
        $this->authorize('own', $portfolio);

        $portfolio = $this->service->update($portfolio, $request->validated());

        return $this->jsonSuccess(PortfolioResource::make($portfolio));
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
        $this->authorize('own', $portfolio);

        $this->service->delete($portfolio);

        return $this->jsonSuccess([], __('Portfolio deleted successfully'));
    }

    /**
     * Add portfolio attachment
     *
     * @param AddPortfolioAttachmentRequest $request
     * @param Portfolio $portfolio
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function addAttachment(AddPortfolioAttachmentRequest $request, Portfolio $portfolio): JsonResponse
    {
        $this->authorize('own', $portfolio);

        $portfolio = $this->service->addAttachment(request()->user(), $portfolio, $request->validated());

        return $this->jsonSuccess(PortfolioResource::make($portfolio));
    }

    /**
     * Add portfolio attachment
     *
     * @param Portfolio $portfolio
     * @param Attachment $attachment
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function deleteAttachment(Portfolio $portfolio, Attachment $attachment): JsonResponse
    {
        $this->authorize('own', $portfolio);

        $portfolio = $this->service->deleteAttachment($portfolio, $attachment);

        return $this->jsonSuccess(PortfolioResource::make($portfolio));
    }

    /**
     * Toggle favorite portfolio
     */
    public function toggleFavorite(Portfolio $portfolio): JsonResponse
    {
        $isFavorite = $this->service->toggleFavorite(request()->user(), $portfolio);

        return $this->jsonSuccess(
            ['is_favorite' => $isFavorite],
            $isFavorite ? __('Portfolio added to favorites') : __('Portfolio removed from favorites')
        );
    }

    /**
     * Toggle portfolio hidden status
     */
    public function toggleHidden(Portfolio $portfolio): JsonResponse
    {
        $this->authorize('own', $portfolio);

        $portfolio = $this->service->toggleHidden($portfolio);

        return $this->jsonSuccess(PortfolioResource::make($portfolio), __('Portfolio status toggled successfully'));
    }

    /**
     * List user's favorite portfolios
     */
    public function favorites(Request $request): JsonResponse
    {
        $portfolios = $this->service->favorites(
            $request->user(),
            $request->input('limit', Setting::PAGE_RESULT_LIMIT),
            $request->input('page', Setting::PAGE),
        );

        return $this->jsonSuccess(PortfolioResource::collection($portfolios));
    }

    /**
     * Add skills to portfolio
     *
     * @param AddPortfolioSkillsRequest $request
     * @param Portfolio $portfolio
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function addSkills(AddPortfolioSkillsRequest $request, Portfolio $portfolio): JsonResponse
    {
        $this->authorize('own', $portfolio);

        $portfolio = $this->service->storePortfolioSkills($portfolio, $request->validated()['skill_ids']);

        return $this->jsonSuccess(PortfolioResource::make($portfolio), __('Skills added successfully'));
    }

    /**
     * Remove skills from portfolio
     *
     * @param RemovePortfolioSkillsRequest $request
     * @param Portfolio $portfolio
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function removeSkills(RemovePortfolioSkillsRequest $request, Portfolio $portfolio): JsonResponse
    {
        $this->authorize('own', $portfolio);

        $portfolio = $this->service->removePortfolioSkills($portfolio, $request->validated()['skill_ids']);

        return $this->jsonSuccess(PortfolioResource::make($portfolio), __('Skills removed successfully'));
    }
}
