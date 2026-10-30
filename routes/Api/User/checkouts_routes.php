<?php

use App\Http\Controllers\Api\Checkout\CheckoutStatusController;
use App\Http\Controllers\Api\Checkout\MyFatoorahPaymentMethodsController;
use App\Http\Controllers\Api\Checkout\OrderCheckoutController;
use App\Http\Controllers\Api\Checkout\ProjectCheckoutController;
use App\Http\Controllers\Api\Checkout\ServiceCheckoutController;
use Illuminate\Support\Facades\Route;

Route::prefix('checkouts')->middleware('auth:api')->group(callback: function () {
    Route::post('/service', [ServiceCheckoutController::class, 'checkout']);
    Route::post('/service/preview', [ServiceCheckoutController::class, 'preview']);
    Route::post('/order', [OrderCheckoutController::class, 'checkout']);
    Route::post('/order/preview', [OrderCheckoutController::class, 'preview']);
    Route::post('/project', [ProjectCheckoutController::class, 'checkout']);
    Route::post('/project/preview', [ProjectCheckoutController::class, 'preview']);
    Route::get('/payment-methods', [MyFatoorahPaymentMethodsController::class, 'checkout']);
    Route::get('/{reference}/status', [CheckoutStatusController::class, 'show'])
        ->middleware('throttle:30,1')
        ->name('checkouts.status');
});
