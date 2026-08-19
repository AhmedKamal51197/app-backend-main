<?php

namespace App\Http\Controllers\Api\Faq;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\Api\Faq\FaqResource;
use App\Models\Faq;
use App\Models\Setting;
use App\Services\Faq\FaqService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FaqController extends BaseApiController
{
    /**
     * Load the service in constructor
     */
    public function __construct(private readonly FaqService $service) {}

    /**
     * Get all FAQs
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $faqs = $this->service->index(
            $request->input('limit', Setting::PAGE_RESULT_LIMIT),
            $request->input('type', ''),
            true
        );

        return $this->jsonSuccess(FaqResource::collection($faqs));
    }

    /**
     * Show FAQ
     *
     * @param Faq $faq
     *
     * @return JsonResponse
     */
    public function show(Faq $faq): JsonResponse
    {
        if (!$faq->is_active) {
            return $this->jsonError('FAQ not found', 404);
        }
        return $this->jsonSuccess(FaqResource::make($faq));
    }
}
