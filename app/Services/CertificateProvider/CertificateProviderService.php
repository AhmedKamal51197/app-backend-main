<?php

namespace App\Services\CertificateProvider;

use App\Actions\Attachments\StoreAttachmentAction;
use App\Actions\Files\GuessFileTypeAction;
use App\Enums\AttachmentDocumentTypeEnum;
use App\Enums\AttachmentStorageEnum;
use App\Events\LogExceptionEvent;
use App\Models\CertificateProvider;
use App\Models\Setting;
use App\Models\User;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * A class defines the certificate provider service
 */
class CertificateProviderService
{
    /**
     * Get all certificate providers
     *
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function index(int $perPage = Setting::PAGE_RESULT_LIMIT): LengthAwarePaginator
    {
        return CertificateProvider::query()
            ->orderByDesc('created_at')
            ->with('logo')
            ->paginate($perPage);
    }

    /**
     * Store new certificate provider
     *
     * @param User $user
     * @param array $data
     *
     * @return CertificateProvider
     *
     * @throws Exception
     */
    public function store(User $user, array $data): CertificateProvider
    {
        DB::beginTransaction();
        try {
            $certificateProvider = CertificateProvider::create([
                'title_ar' => $data['title_ar'],
                'title_en' => $data['title_en'],
                'slug' => $data['slug'] ?? null,
                'description' => $data['description'] ?? null,
                'website_url' => $data['website_url'] ?? null,
                'is_active' => $data['is_active'] ?? true
            ]);

            // Handle logo upload if provided
            if (isset($data['logo']) && $data['logo']) {
                StoreAttachmentAction::store($certificateProvider, $data['logo'], 'attachments', false);
            }

            $certificateProvider->load(['logo']);

            DB::commit();

            return $certificateProvider;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Update Certificate Provider
     *
     * @param CertificateProvider $certificateProvider
     * @param array $data
     *
     * @return CertificateProvider
     *
     * @throws Exception
     */
    public function update(CertificateProvider $certificateProvider, array $data): CertificateProvider
    {
        DB::beginTransaction();
        try {
            $updateData = array_filter([
                'title_ar' => $data['title_ar'] ?? $certificateProvider->title_ar,
                'title_en' => $data['title_en'] ?? $certificateProvider->title_en,
                'slug' => $data['slug'] ?? $certificateProvider->slug,
                'description' => $data['description'] ?? $certificateProvider->description,
                'website_url' => $data['website_url'] ?? $certificateProvider->website_url,
            ], function ($value) {
                return $value !== null;
            });

            $certificateProvider->update($updateData);

            DB::commit();

            return $certificateProvider;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Delete certificate provider
     *
     * @param CertificateProvider $certificateProvider
     *
     * @return bool
     *
     * @throws Exception
     */
    public function delete(CertificateProvider $certificateProvider): bool
    {
        DB::beginTransaction();
        try {
            $result = $certificateProvider->delete();

            DB::commit();

            return $result;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }
}
