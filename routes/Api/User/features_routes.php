<?php

use App\Http\Controllers\Api\Feature\FeatureController;
use Illuminate\Support\Facades\Route;

Route::prefix('features')->group(function () {
    Route::get('/', [FeatureController::class, 'index']);
    Route::get('/{feature}', [FeatureController::class, 'show']);
});