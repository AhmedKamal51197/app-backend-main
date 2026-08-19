<?php

namespace App\Services\Kyc;

use App\Actions\Attachments\StoreAttachmentAction;
use App\Enums\KycStatusEnum;
use App\Events\LogExceptionEvent;
use App\Models\Country;
use App\Models\Kyc;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\Kyc\KycApprovedNotification;
use App\Notifications\Kyc\KycRejectedNotification;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * A class defines the KYC service
 */
class KycService
{
    /**
     * Index the KYCS
     *
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function index(int $perPage = Setting::PAGE_RESULT_LIMIT): LengthAwarePaginator
    {
        return Kyc::query()
            ->with('country')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Index the KYCS by status
     *
     * @param string $status
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function indexByStatus(string $status, int $perPage = Setting::PAGE_RESULT_LIMIT): LengthAwarePaginator
    {
        return Kyc::query()
            ->with('country')
            ->orderByDesc('created_at')
            ->where('status', '=', $status)
            ->paginate($perPage);
    }

    /**
     * Index user KYCS
     *
     * @param User $user
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function userKycs(User $user, int $perPage = Setting::PAGE_RESULT_LIMIT): LengthAwarePaginator
    {
        return Kyc::query()
            ->with('country')
            ->orderByDesc('created_at')
            ->where('user_id', '=', $user->getAttribute('id'))
            ->paginate($perPage);
    }

    /**
     * Store new KYC request to admin
     *
     * @param User $user
     * @param array $data
     *
     * @return Kyc
     *
     * @throws Exception
     */
    public function store(User $user, array $data): Kyc
    {
        DB::beginTransaction();
        try {
            $kyc = Kyc::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'user_id' => $user->getAttribute('id'),
                'country_id' => Country::where('uuid',$data['country_id'])->value('id') ,
                'birth_date' => Carbon::parse($data['birth_date']),
                'status' => KycStatusEnum::PENDING->value,
            ]);

            if (isset($data['kyc_attachments'])) {
                StoreAttachmentAction::store($kyc, $data['kyc_attachments'], 'attachments');
            }

            $kyc->load(['attachments', 'user', 'country']);

            DB::commit();

            return $kyc;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Accept the KYC
     *
     * @param Kyc $kyc
     *
     * @return Kyc
     *
     * @throws Exception
     */
    public function accept(Kyc $kyc): Kyc
    {
        DB::beginTransaction();
        try {
            $kyc->update([
                'status' => KycStatusEnum::APPROVED->value,
            ]);

            $kyc->user->notify(new (KycApprovedNotification::class));

            DB::commit();

            return $kyc;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Reject the KYC Request
     *
     * @param Kyc $kyc
     *
     * @return Kyc
     *
     * @throws Exception
     */
    public function reject(Kyc $kyc): Kyc
    {
        DB::beginTransaction();
        try {
            $kyc->update([
                'status' => KycStatusEnum::REJECTED->value,
            ]);

            $kyc->user->notify(new (KycRejectedNotification::class));

            DB::commit();

            return $kyc;

        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }
}
