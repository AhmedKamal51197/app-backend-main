<?php

use App\Http\Controllers\Admin\Refund\RefundController;
use Illuminate\Support\Facades\Route;

Route::prefix('refunds')->group(callback: function () {
    Route::get('/', [RefundController::class, 'index'])->middleware('permission:Index Refunds');
    Route::prefix('{refund}')->group(function () {
        Route::get('/', [RefundController::class, 'show'])->middleware('permission:Details Refunds');
    });
});