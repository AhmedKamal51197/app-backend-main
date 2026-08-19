<?php

use App\Http\Controllers\Api\Provider\ProviderController;
use Illuminate\Support\Facades\Route;

Route::prefix('providers')->middleware('auth:api')->group(function () {
    Route::prefix('{user}')->group(function () {
        Route::get('/', [ProviderController::class, 'profile']);
    });
});
