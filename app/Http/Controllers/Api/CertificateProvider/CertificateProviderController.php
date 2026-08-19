<?php

namespace App\Http\Controllers\Api\CertificateProvider;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Http\Resources\Api\CertificateProvider\CertificateProviderResource;
use App\Models\CertificateProvider;
use App\Models\Setting;
use App\Services\CertificateProvider\CertificateProviderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * A class defined for the certificate provider controller
 */
class CertificateProviderController extends BaseAdminController
{
    /**
     * Load the service
     *
     * @param CertificateProviderService $service
     */
    public function __construct(protected CertificateProviderService $service)
    {
    }

    /**
     * List of certificate providers
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $certificateProviders = $this->service->index($request->input('limit', Setting::PAGE_RESULT_LIMIT));

        return $this->jsonSuccess(CertificateProviderResource::collection($certificateProviders));
    }

    /**
     * Show certificate provider
     *
     * @param CertificateProvider $certificateProvider
     *
     * @return JsonResponse
     */
    public function show(CertificateProvider $certificateProvider): JsonResponse
    {
        $certificateProvider->load(['certificates', 'logo']);

        return $this->jsonSuccess(CertificateProviderResource::make($certificateProvider));
    }
}
