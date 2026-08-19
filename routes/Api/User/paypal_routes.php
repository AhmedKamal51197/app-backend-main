<?php

use App\Http\Controllers\Api\PayPal\PayPalController;
use Illuminate\Support\Facades\Route;

// User PayPal routes
Route::prefix('paypal')->middleware('auth:api')->group(function () {
    Route::get('/', [PayPalController::class, 'userPayPal']);
    Route::post('/store', [PayPalController::class, 'store']);
});
