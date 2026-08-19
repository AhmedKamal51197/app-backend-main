<?php

namespace App\Services\Certificate;

use App\Events\LogExceptionEvent;
use App\Models\Certificate;
use App\Models\CertificateProvider;
use App\Models\Setting;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * A class defines the certificate service
 */
class CertificateService
{
    /**
     * Get all certificates
     *
     * @param CertificateProvider $certificateProvider
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function index(CertificateProvider $certificateProvider, int $perPage = Setting::PAGE_RESULT_LIMIT): LengthAwarePaginator
    {
        return Certificate::query()
            ->where('provider_id', '=', $certificateProvider->getAttribute('id'))
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Store new certificate
     *
     * @param CertificateProvider $certificateProvider
     * @param array $data
     *
     * @return Certificate
     *
     * @throws Exception
     */
    public function store(CertificateProvider $certificateProvider, array $data): Certificate
    {
        DB::beginTransaction();
        try {
            $certificate = Certificate::create([
                'title_ar' => $data['title_ar'],
                'title_en' => $data['title_en'] ?? null,
                'description' => $data['description'] ?? null,
                'level' => $data['level'] ?? null,
                'provider_id' => $certificateProvider->getAttribute('id'),
                'is_active' => true
            ]);

            DB::commit();

            return $certificate;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Update Certificate
     *
     * @param Certificate $certificate
     * @param array $data
     *
     * @return Certificate
     *
     * @throws Exception
     */
    public function update(Certificate $certificate, array $data): Certificate
    {
        DB::beginTransaction();
        try {
            $updateData = array_filter([
                'title_ar' => $data['title_ar'] ?? $certificate->title_ar,
                'title_en' => $data['title_en'] ?? $certificate->title_en,
                'description' => $data['description'] ?? $certificate->description,
                'level' => $data['level'] ?? $certificate->level,
            ], function ($value) {
                return $value !== null;
            });

            $certificate->update($updateData);

            DB::commit();

            return $certificate;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Delete certificate
     *
     * @param Certificate $certificate
     *
     * @return bool
     *
     * @throws Exception
     */
    public function delete(Certificate $certificate): bool
    {
        DB::beginTransaction();
        try {
            $result = $certificate->delete();

            DB::commit();

            return $result;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }
}
