<?php

namespace App\Http\Controllers\Admin\Faq;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Admin\Faq\StoreFaqRequest;
use App\Http\Requests\Admin\Faq\UpdateFaqRequest;
use App\Http\Resources\Admin\Faq\FaqResource;
use App\Models\Faq;
use App\Models\Setting;
use App\Services\Faq\FaqService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FaqController extends BaseApiController
{
    public function __construct(private FaqService $service) {}

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
            $request->input('type', '')
        );

        return $this->jsonSuccess(FaqResource::collection($faqs));
    }

    /**
     * Store new FAQ
     *
     * @param StoreFaqRequest $request
     *
     * @return JsonResponse
     *
     * @throws \Exception
     */
    public function store(StoreFaqRequest $request): JsonResponse
    {
        $faq = $this->service->create($request->validated());

        return $this->jsonSuccess(FaqResource::make($faq), (__('FAQ created successfully')));
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
        return $this->jsonSuccess(FaqResource::make($faq));
    }

    /**
     * Update FAQ
     *
     * @param UpdateFaqRequest $request
     *
     * @param Faq $faq
     */
    public function update(UpdateFaqRequest $request, Faq $faq): JsonResponse
    {
        $faq = $this->service->update($faq, $request->validated());

        return $this->jsonSuccess(FaqResource::make($faq), (__('FAQ updated successfully')));
    }

    /**
     * Delete FAQ
     *
     * @param Faq $faq
     *
     * @return JsonResponse
     */
    public function destroy(Faq $faq): JsonResponse
    {
        $this->service->delete($faq);

        return $this->jsonSuccess([], 'FAQ deleted successfully');
    }

    /**
     * Toggle FAQ active status
     *
     * @param Faq $faq
     *
     * @return JsonResponse
     */
    public function toggleActive(Faq $faq): JsonResponse
    {
        $faq = $this->service->toggleActive($faq);
        return $this->jsonSuccess(FaqResource::make($faq), __('FAQ active status toggled successfully'));
    }
}
