<?php

namespace App\Http\Controllers\Admin\Service;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Http\Requests\Admin\Service\ServiceIndexRequest;
use App\Http\Resources\Admin\Service\ServiceResource;
use App\Enums\OrderDirectionEnum;
use App\Enums\TimePeriodEnum;
use App\Models\Service;
use App\Models\Setting;
use App\Services\Service\AdminServiceService;
use Illuminate\Http\JsonResponse;

/**
 * A class defines the Service controller
 */
class ServiceController extends BaseAdminController
{
    /**
     * Call the service
     *
     * @param AdminServiceService $adminServiceService
     */
    public function __construct(protected AdminServiceService $adminServiceService)
    {
    }

    /**
     * List of the system Services
     *
     * @param ServiceIndexRequest $request
     *
     * @return JsonResponse
     */
    public function index(ServiceIndexRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        $data = [
            'search' => $validated['search'] ?? '',
            'limit' => $validated['limit'] ?? Setting::PAGE_RESULT_LIMIT,
            'page' => $validated['page'] ?? Setting::PAGE,
            'status' => $validated['status'] ?? null,
            'order_by' => $validated['order_by'] ?? OrderDirectionEnum::LATEST->value,
            'services_categories_rate' => $validated['services_categories_rate'] ?? TimePeriodEnum::MONTHLY->value,
        ];

        $result = $this->adminServiceService->index($data);

        return response()->json([
            'error' => false,
            'message' => '',
            'data' => ServiceResource::collection($result['services']),
            'analytics' => $result['analytics'],
            'meta' => $result['meta'],
        ]);
    }

    /**
     * Show Service
     *
     * @param Service $service
     *
     * @return JsonResponse
     */
    public function show(Service $service): JsonResponse
    {
        $service->load([
            'user',
            'category',
            'subCategory',
            'attachments',
            'packages.features',
            'rates',
            'skills.skill',
            'orders.seeker',
        ]);

        $analytics = $this->adminServiceService->getServiceAnalytics($service);

        $response = $this->jsonSuccess(ServiceResource::make($service));
        $responseData = json_decode($response->getContent(), true);

        $responseData['analytics'] = $analytics;

        return response()->json($responseData);
    }

    /**
     * Toggle service approval status
     *
     * @param Service $service
     *
     * @return JsonResponse
     */
    public function approve(Service $service): JsonResponse
    {
        $service->update(['is_approved' => !$service->is_approved]);

        return $this->jsonSuccess(ServiceResource::make($service));
    }

    /**
     * Toggle service hidden status
     *
     * @param Service $service
     *
     * @return JsonResponse
     */
    public function toggleHidden(Service $service): JsonResponse
    {
        $service->update(['hidden' => !$service->hidden]);

        return $this->jsonSuccess(ServiceResource::make($service));
    }

    /**
     * Delete service
     *
     * @param Service $service
     *
     * @return JsonResponse
     */
    public function delete(Service $service): JsonResponse
    {
        $service->delete();

        return response()->json([
            'error' => false,
            'message' => 'Service deleted successfully',
        ]);
    }
}
