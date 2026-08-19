<?php

use App\Http\Controllers\Api\Kyc\KycController;
use Illuminate\Support\Facades\Route;

Route::prefix('kycs')->middleware('auth:api')->group(callback: function () {
    Route::get('/', [KycController::class, 'index']);
    Route::post('/add', [KycController::class, 'store']);
    Route::prefix('{kyc}')->group(function () {
        Route::get('/', [KycController::class, 'show']);
    });
});
