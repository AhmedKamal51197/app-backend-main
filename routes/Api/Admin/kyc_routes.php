<?php

use App\Http\Controllers\Admin\Kyc\KycController;
use Illuminate\Support\Facades\Route;

Route::prefix('kycs')->group(function () {
    Route::get('/', [KycController::class, 'index'])->middleware('permission:Details Users');
    Route::get('/pending', [KycController::class, 'pending'])->middleware('permission:Index KYC');
    Route::get('/approved', [KycController::class, 'approved'])->middleware('permission:Index KYC');
    Route::get('/rejected', [KycController::class, 'rejected'])->middleware('permission:Index KYC');
    Route::prefix('{kyc}')->group(function () {
        Route::get('/', [KycController::class, 'show'])->middleware('permission:Details KYC');
        Route::post('approve', [KycController::class, 'approve'])->middleware('permission:Approve KYC');
        Route::post('reject', [KycController::class, 'reject'])->middleware('permission:Reject KYC');
        Route::delete('delete', [KycController::class, 'delete'])->middleware('permission:Delete KYC');
    });
});
