<?php

use App\Http\Controllers\Api\Auth\ForgetPasswordController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

/**
 * Authentication routes
 */
Route::post('/login', LoginController::class);
Route::post('/register', RegisterController::class);
Route::post('/logout', LogoutController::class)->middleware('auth:api');
Route::prefix('password')->group(function () {
    Route::post('/forget', [ForgetPasswordController::class, 'forget'])->name('password.request');
    Route::post('/reset', [ForgetPasswordController::class, 'reset'])->name('password.reset');
});


/**
 * Reset Password Routes
 */
require('Api/User/reset_password_routes.php');
