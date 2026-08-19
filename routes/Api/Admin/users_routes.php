<?php

use App\Http\Controllers\Admin\Users\UserDetailsController;
use App\Http\Controllers\Admin\Users\UsersController;
use Illuminate\Support\Facades\Route;

Route::prefix('users')->group(function () {
    Route::get('/', [UsersController::class, 'index'])->middleware('permission:Index Users');
    Route::get('/analytics', [UsersController::class, 'analytics'])->middleware('permission:Index Users');
    Route::get('/seekers', [UsersController::class, 'seekers'])->middleware('permission:Index Users');
    Route::get('/providers', [UsersController::class, 'providers'])->middleware('permission:Index Users');
    Route::get('/admins', [UsersController::class, 'admins'])->middleware('permission:Index Users');
    Route::prefix('{user}')->group(function () {
        Route::get('/', [UsersController::class, 'show'])->middleware('permission:Details Users');
        Route::get('/wallet', [UserDetailsController::class, 'wallet'])->middleware('permission:Index Wallets');
        Route::get('/payment-requests', [UserDetailsController::class, 'paymentRequests'])->middleware('permission:Index Payment Requests');
        Route::get('/orders', [UserDetailsController::class, 'orders'])->middleware('permission:Index Orders');
        Route::get('/kyc', [UserDetailsController::class, 'kycs'])->middleware('permission:Index KYC');
        Route::get('/services', [UserDetailsController::class, 'services'])->middleware('permission:Index Services');
        Route::post('/toggle-active', [UsersController::class, 'toggleActive'])->middleware('permission:Toggle User Status');
        Route::delete('/delete', [UsersController::class, 'delete']);
    });
});