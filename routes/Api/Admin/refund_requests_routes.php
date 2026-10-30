<?php

use App\Http\Controllers\Admin\RefundRequest\RefundRequestController;
use Illuminate\Support\Facades\Route;

// Admin approve/reject for refund requests (financial-flow refunds table).
Route::prefix('refund-requests')->group(function () {
    Route::prefix('{refundRequest}')->group(function () {
        // Reuses the payment-requests permissions so the same admin who manages
        // withdrawals can approve/reject refunds without extra role setup.
        Route::post('approve', [RefundRequestController::class, 'approve'])
            ->middleware('permission:Approve Payment Requests');

        Route::post('reject', [RefundRequestController::class, 'reject'])
            ->middleware('permission:Reject Payment Requests');
    });
});
