<?php

namespace App\Http\Controllers\Admin\Certificate;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Http\Requests\Admin\Certificate\StoreCertificateRequest;
use App\Http\Requests\Admin\Certificate\UpdateCertificateRequest;
use App\Http\Resources\Admin\Certificate\CertificateResource;
use App\Models\Certificate;
use App\Models\CertificateProvider;
use App\Models\Setting;
use App\Services\Certificate\CertificateService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * A class defines the certificate controller for admin
 */
class CertificateController extends BaseAdminController
{
    /**
     * Call the service
     *
     * @param CertificateService $service
     */
    public function __construct(protected CertificateService $service)
    {
    }

    /**
     * List of certificates
     *
     * @param CertificateProvider $certificateProvider
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(CertificateProvider $certificateProvider, Request $request): JsonResponse
    {
        $certificates = $this->service->index($certificateProvider, $request->input('limit', Setting::PAGE_RESULT_LIMIT));

        return $this->jsonSuccess(CertificateResource::collection($certificates));
    }

    /**
     * Show certificate
     *
     * @param Certificate $certificate
     *
     * @return JsonResponse
     */
    public function show(Certificate $certificate): JsonResponse
    {
        $certificate->load(['provider']);

        return $this->jsonSuccess(CertificateResource::make($certificate));
    }

    /**
     * Store certificate
     *
     * @param CertificateProvider $certificateProvider
     * @param StoreCertificateRequest $request
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function store(CertificateProvider $certificateProvider, StoreCertificateRequest $request): JsonResponse
    {
        $certificate = $this->service->store($certificateProvider, $request->validated());

        return $this->jsonSuccess(
            CertificateResource::make($certificate),
            __('Certificate created successfully')
        );
    }

    /**
     * Update certificate
     *
     * @param UpdateCertificateRequest $request
     * @param Certificate $certificate
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function edit(UpdateCertificateRequest $request, Certificate $certificate): JsonResponse
    {
        $certificate = $this->service->update($certificate, $request->validated());

        return $this->jsonSuccess(
            CertificateResource::make($certificate),
            __('Certificate updated successfully')
        );
    }

    /**
     * Delete Certificate
     *
     * @param Certificate $certificate
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function delete(Certificate $certificate): JsonResponse
    {
        $this->service->delete($certificate);

        return $this->jsonSuccess([], __('Certificate deleted successfully'));
    }
}
