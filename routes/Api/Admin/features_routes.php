<?php

use App\Http\Controllers\Admin\Feature\FeatureController;
use Illuminate\Support\Facades\Route;

Route::prefix('features')->group(function () {
    Route::get('/', [FeatureController::class, 'index']);
    Route::post('/add', [FeatureController::class, 'store']);
    Route::prefix('{feature}')->group(function () {
        Route::get('/', [FeatureController::class, 'show']);
        Route::post('/edit', [FeatureController::class, 'update']);
        Route::delete('/delete', [FeatureController::class, 'delete']);
    });
});