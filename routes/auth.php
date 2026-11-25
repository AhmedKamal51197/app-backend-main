<?php

use App\Http\Controllers\Api\Auth\ForgetPasswordController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\RegistrationOtpController;
use Illuminate\Support\Facades\Route;

/**
 * Authentication routes
 */
Route::post('/login', LoginController::class);
// Old one-step sign-up (account created before the email is confirmed);
// kept for app versions that do not use /register/start yet.
Route::post('/register', RegisterController::class);

// Sign-up confirmed by email OTP: the account is created only after /verify.
Route::prefix('register')->middleware('throttle:20,1')->group(function () {
    Route::post('/start', [RegistrationOtpController::class, 'start'])->name('register.start');
    Route::post('/resend', [RegistrationOtpController::class, 'resend'])->name('register.resend');
    Route::post('/verify', [RegistrationOtpController::class, 'verify'])->name('register.verify');
});
Route::post('/logout', LogoutController::class)->middleware('auth:api');
Route::prefix('password')->group(function () {
    Route::post('/forget', [ForgetPasswordController::class, 'forget'])->name('password.request');
    Route::post('/reset', [ForgetPasswordController::class, 'reset'])->name('password.reset');
});


/**
 * Reset Password Routes
 */
require('Api/User/reset_password_routes.php');
