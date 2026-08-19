<?php

namespace App\Http\Controllers\Admin\CertificateProvider;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Http\Requests\Admin\CertificateProvider\StoreCertificateProviderRequest;
use App\Http\Requests\Admin\CertificateProvider\UpdateCertificateProviderRequest;
use App\Http\Resources\Admin\CertificateProvider\CertificateProviderResource;
use App\Models\CertificateProvider;
use App\Models\Setting;
use App\Services\CertificateProvider\CertificateProviderService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * A class defines the certificate provider controller for admin
 */
class CertificateProviderController extends BaseAdminController
{
    /**
     * Call the service
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

    /**
     * Store certificate provider
     *
     * @param StoreCertificateProviderRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function store(StoreCertificateProviderRequest $request): JsonResponse
    {
        $certificateProvider = $this->service->store(request()->user(), $request->validated());

        return $this->jsonSuccess(
            CertificateProviderResource::make($certificateProvider),
            __('Certificate Provider created successfully')
        );
    }

    /**
     * Update certificate provider
     *
     * @param UpdateCertificateProviderRequest $request
     * @param CertificateProvider $certificateProvider
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function edit(UpdateCertificateProviderRequest $request, CertificateProvider $certificateProvider): JsonResponse
    {
        $certificateProvider = $this->service->update($certificateProvider, $request->validated());

        return $this->jsonSuccess(
            CertificateProviderResource::make($certificateProvider),
            __('Certificate Provider updated successfully')
        );
    }

    /**
     * Delete Certificate Provider
     *
     * @param CertificateProvider $certificateProvider
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function delete(CertificateProvider $certificateProvider): JsonResponse
    {
        $this->service->delete($certificateProvider);

        return $this->jsonSuccess([], __('Certificate Provider deleted successfully'));
    }
}
