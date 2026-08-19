<?php

use App\Http\Controllers\Api\ResetPassword\ResetPasswordController;
use Illuminate\Support\Facades\Route;

Route::prefix('reset-password')->group(callback: function () {
    Route::post('/send/otp', [ResetPasswordController::class, 'sendOtp']);
    Route::post('/verify/otp', [ResetPasswordController::class, 'verifyOtp']);
    Route::post('/reset', [ResetPasswordController::class, 'resetPassword']);
});
