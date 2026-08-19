<?php

use App\Http\Controllers\Api\Banner\BannerController;
use Illuminate\Support\Facades\Route;

Route::prefix('banners')->group(function () {
    Route::get('/', [BannerController::class, 'index']);
    Route::get('{banner}', [BannerController::class, 'show']);
    Route::get('key/{banner:key}', [BannerController::class, 'show']);
});
