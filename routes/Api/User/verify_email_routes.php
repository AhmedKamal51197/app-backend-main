<?php

use App\Http\Controllers\Api\EmailVerification\EmailVerificationController;
use Illuminate\Support\Facades\Route;

Route::prefix('verify-email')->middleware('auth:api')->group(function () {
    Route::post('/send', [EmailVerificationController::class, 'send']);
    Route::post('/verify', [EmailVerificationController::class, 'verify']);
});
