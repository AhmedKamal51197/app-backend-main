<?php

use App\Http\Controllers\Admin\PaymentRequest\PaymentRequestController;
use Illuminate\Support\Facades\Route;

Route::prefix('payment-requests')->group(function () {
    Route::get('/', [PaymentRequestController::class, 'index'])->middleware('permission:Index Payment Requests');
    Route::get('/pending', [PaymentRequestController::class, 'pending'])->middleware('permission:Index Payment Requests');
    Route::get('/approved', [PaymentRequestController::class, 'approved'])->middleware('permission:Index Payment Requests');
    Route::get('/rejected', [PaymentRequestController::class, 'rejected'])->middleware('permission:Index Payment Requests');
    Route::prefix('{paymentRequest}')->group(function () {
        Route::get('/', [PaymentRequestController::class, 'show'])->middleware('permission:Details Payment Requests');
        Route::post('approve', [PaymentRequestController::class, 'approve'])->middleware('permission:Approve Payment Requests');
        Route::post('reject', [PaymentRequestController::class, 'reject'])->middleware('permission:Reject Payment Requests');
    });
});
