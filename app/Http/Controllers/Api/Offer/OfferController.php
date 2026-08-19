<?php

namespace App\Http\Controllers\Api\Offer;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\Offer\StoreOfferRequest;
use App\Http\Resources\Api\Service\ServiceResource;
use App\Models\Chat;
use App\Models\Service;
use App\Models\Setting;
use App\Services\Offer\OfferService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * A class defines the offer controller
 */
class OfferController extends BaseApiController
{
    /**
     * Load the service
     *
     * @param OfferService $service
     */
    public function __construct(protected OfferService $service)
    {
    }

    /**
     * List of the user services
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $services = $this->service->index(request()->user(), $request->input('limit', Setting::PAGE_RESULT_LIMIT));

        return $this->jsonSuccess(ServiceResource::collection($services));
    }

    /**
     * Show the service
     *
     * @param Service $service
     *
     * @return JsonResponse
     */
    public function show(Service $service): JsonResponse
    {
        $service->load(['attachments', 'category', 'skills', 'skills.skill', 'packages', 'subCategory']);

        return $this->jsonSuccess(ServiceResource::make($service));
    }

    /**
     * Store offer data
     *
     * @param Chat $chat
     * @param StoreOfferRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function store(Chat $chat, StoreOfferRequest $request): JsonResponse
    {
        $service = $this->service->store($chat, request()->user(), $request->validated());

        return $this->jsonSuccess(
            ServiceResource::make($service),
            __('Offer created successfully')
        );
    }
}
