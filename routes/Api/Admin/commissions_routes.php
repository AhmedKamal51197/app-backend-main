<?php

use App\Http\Controllers\Admin\Commission\CommissionController;
use Illuminate\Support\Facades\Route;

Route::prefix('commissions')->group(callback: function () {
    Route::get('/', [CommissionController::class, 'index'])->middleware('permission:Index Commissions');
    Route::prefix('{commission}')->group(function () {
        Route::get('/', [CommissionController::class, 'show'])->middleware('permission:Details Commissions');
    });
});
