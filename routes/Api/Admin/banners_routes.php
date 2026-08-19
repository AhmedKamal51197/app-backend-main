<?php

use App\Http\Controllers\Admin\Banner\BannerController;
use Illuminate\Support\Facades\Route;

Route::prefix('banners')->group(function () {
    Route::get('/', [BannerController::class, 'index'])->middleware('permission:Index Banners');
    Route::post('/add', [BannerController::class, 'store'])->middleware('permission:Add Banners');
    Route::prefix('/{banner}')->group(function () {
        Route::get('/', [BannerController::class, 'show'])->middleware('permission:Details Banners');
        Route::post('/edit', [BannerController::class, 'update'])->middleware('permission:Edit Banners');
        Route::delete('/delete', [BannerController::class, 'destroy'])->middleware('permission:Delete Banners');
        Route::post('/toggle-active', [BannerController::class, 'toggleActive'])->middleware('permission:Toggle Banner Status');
    });
});