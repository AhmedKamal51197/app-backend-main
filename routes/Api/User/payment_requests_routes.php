<?php

use App\Http\Controllers\Api\PaymentRequest\PaymentRequestController;
use Illuminate\Support\Facades\Route;

// Payment Requests routes
Route::prefix('payment-requests')->middleware('auth:api')->group(function () {
    Route::get('/', [PaymentRequestController::class, 'index']);
    Route::post('/add', [PaymentRequestController::class, 'store']);
    Route::prefix('{paymentRequest}')->group(function () {
        Route::get('/', [PaymentRequestController::class, 'show']);
    });
});
