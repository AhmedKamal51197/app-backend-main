<?php

namespace App\Services\RefundRequest;

use App\Enums\RefundRequestStatusEnum;
use App\Events\LogExceptionEvent;
use App\Models\RefundRequest;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * Admin actions on refund requests. The actual money movement is handled
 * manually by the admin (bank transfer); these actions record the in-system
 * confirmation (approve) or the rejection with a reason.
 */
class RefundRequestService
{
    /**
     * Approve a refund request (confirm the manual refund was done).
     *
     * @param RefundRequest $refundRequest
     *
     * @return RefundRequest
     *
     * @throws Exception
     */
    public function approve(RefundRequest $refundRequest): RefundRequest
    {
        DB::beginTransaction();
        try {
            $refundRequest->update([
                'status' => RefundRequestStatusEnum::APPROVED->value,
                'released_at' => now(),
            ]);

            DB::commit();

            return $refundRequest;
        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }

    /**
     * Reject a refund request, storing the admin's reason.
     *
     * @param RefundRequest $refundRequest
     * @param string|null $reason
     *
     * @return RefundRequest
     *
     * @throws Exception
     */
    public function reject(RefundRequest $refundRequest, ?string $reason = null): RefundRequest
    {
        DB::beginTransaction();
        try {
            $refundRequest->update([
                'status' => RefundRequestStatusEnum::REJECTED->value,
                'reason' => $reason !== null && $reason !== '' ? $reason : $refundRequest->reason,
            ]);

            DB::commit();

            return $refundRequest;
        } catch (Exception $exception) {
            DB::rollBack();
            event(new LogExceptionEvent($exception));

            throw $exception;
        }
    }
}
