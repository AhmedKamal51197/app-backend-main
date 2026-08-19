<?php

namespace App\Services\UserCertificate;

use App\Actions\Files\GuessFileTypeAction;
use App\Enums\AttachmentDocumentTypeEnum;
use App\Enums\AttachmentStorageEnum;
use App\Events\LogExceptionEvent;
use App\Models\Certificate;
use App\Models\CertificateUser;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * A class defines the user certificate service
 */
class UserCertificateService
{
    /**
     * Store user certificate without detaching the old ones.
     *
     * @param User $user
     * @param array $data
     *
     * @return CertificateUser
     *
     * @throws Exception
     */
    public function storeUserCertificate(User $user, array $data): CertificateUser
    {
        DB::beginTransaction();
        try {
            $certificate = Certificate::where('uuid', $data['certificate_id'])->firstOrFail();

            $alreadyAttached = $user->userCertificates()
                ->where('certificate_id', $certificate->id)
                ->exists();

            if ($alreadyAttached) {
                throw new Exception(__('User already has this certificate assigned'));
            }

            $certificateUser = CertificateUser::create([
                'certificate_id' => $certificate->id,
                'user_id' => $user->getAttribute('id'),
                'completion_date' => $data['completion_date'] ?? null,
                'expiry_date' => $data['expiry_date'] ?? null,
                'credential_id' => $data['credential_id'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]);

            if (!empty($data['certificate'])) {
                self::uploadCertificate($user, $data['certificate'], $certificateUser);
            }

            DB::commit();

            return $certificateUser->load('file', 'certificate');

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Edit the user certificate
     *
     * @param User $user
     * @param Certificate $certificate
     * @param array $data
     *
     * @return CertificateUser
     *
     * @throws Exception
     */
    public function updateUserCertificate(User $user, Certificate $certificate, array $data): CertificateUser
    {
        DB::beginTransaction();
        try {
            $userCertificate = $user->userCertificates()
                ->where('certificate_id', $certificate->getAttribute('id'))
                ->first();

            $userCertificate->update([
                'completion_date' => $data['completion_date'] ?? $userCertificate->completion_date,
                'expiry_date' => $data['expiry_date'] ?? $userCertificate->expiry_date,
                'credential_id' => $data['credential_id'] ?? $userCertificate->credential_id,
            ]);

            // If a new certificate file is uploaded
            if (!empty($data['certificate'])) {
                StoreAttachmentAction::store($userCertificate, $data['certificate'], 'file', true);
            }

            DB::commit();

            return $userCertificate->load('file', 'certificate');

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Remove certificate from user
     *
     * @param User $user
     * @param Certificate $certificate
     *
     * @return bool
     *
     * @throws Exception
     */
    public function removeUserCertificate(User $user, Certificate $certificate): bool
    {
        DB::beginTransaction();
        try {
            $userCertificate = $user->userCertificates()
                ->where('certificate_id', $certificate->getAttribute('id'))
                ->firstOrFail();

            $result = $userCertificate->delete();

            DB::commit();

            return $result;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Function to store the logo
     *
     * @param User $user
     * @param $certificate
     * @param CertificateUser $certificateUser
     *
     * @return void
     *
     * @throws Exception
     */
    private function uploadCertificate(User $user, $certificate, CertificateUser $certificateUser): void
    {
        $filePath = Storage::disk(AttachmentStorageEnum::BLOB->value)->put('certificates/' . $certificateUser->getAttribute('id'), $certificate);

        // Create logo attachment record
        $certificateUser->file()->create([
            'disk' => AttachmentStorageEnum::BLOB->value,
            'path' => $filePath,
            'file_meta' => json_encode([
                'original_name' => $certificate->getClientOriginalName(),
                'mime_type' => $certificate->getClientMimeType(),
                'size' => $certificate->getSize(),
            ]),
            'user_id' => $user->getAttribute('id'),
            'document_type' => AttachmentDocumentTypeEnum::LOGO->value,
            'type' => GuessFileTypeAction::guess($certificate)->value,
        ]);
    }
}
