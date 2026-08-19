<?php

use App\Http\Controllers\Admin\CommissionSettings\CommissionSettingsController;
use Illuminate\Support\Facades\Route;

Route::prefix('commissions-settings')->group(callback: function () {
    Route::get('/', [CommissionSettingsController::class, 'index'])->middleware('permission:Index Commission Settings');
    Route::prefix('{commissionSetting}')->group(function () {
        Route::get('/', [CommissionSettingsController::class, 'show'])->middleware('permission:Details Commission Settings');
        Route::post('/edit', [CommissionSettingsController::class, 'update'])->middleware('permission:Edit Commission Settings');
        Route::delete('/delete', [CommissionSettingsController::class, 'destroy'])->middleware('permission:Delete Commission Settings');
    });
});