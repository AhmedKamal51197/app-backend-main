<?php

use App\Http\Controllers\Admin\Setting\SettingController;
use Illuminate\Support\Facades\Route;

Route::prefix('settings')->group(callback: function () {
    Route::get('/', [SettingController::class, 'index']);
    Route::post('/withdrawal-settings', [SettingController::class, 'updateWithdrawalSettings'])->middleware('permission:Edit Settings');
    Route::prefix('{setting}')->group(function () {
        Route::get('/', [SettingController::class, 'show']);
        Route::post('/edit', [SettingController::class, 'update']);
        Route::delete('/delete', [SettingController::class, 'destroy']);
    });
});
