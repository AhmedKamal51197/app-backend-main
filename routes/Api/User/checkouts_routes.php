<?php

use App\Http\Controllers\Api\Checkout\MyFatoorahPaymentMethodsController;
use App\Http\Controllers\Api\Checkout\OrderCheckoutController;
use App\Http\Controllers\Api\Checkout\ProjectCheckoutController;
use App\Http\Controllers\Api\Checkout\ServiceCheckoutController;
use Illuminate\Support\Facades\Route;

Route::prefix('checkouts')->middleware('auth:api')->group(callback: function () {
    Route::post('/service', [ServiceCheckoutController::class, 'checkout']);
    Route::post('/order', [OrderCheckoutController::class, 'checkout']);
    Route::post('/project', [ProjectCheckoutController::class, 'checkout']);
    Route::get('/payment-methods', [MyFatoorahPaymentMethodsController::class, 'checkout']);
});
