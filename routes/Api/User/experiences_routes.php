<?php

use App\Http\Controllers\Api\Experience\ExperienceController;
use Illuminate\Support\Facades\Route;

Route::prefix('experiences')->middleware('auth:api')->group(callback: function () {
    Route::get('/', [ExperienceController::class, 'index']);
    Route::get('/info', [ExperienceController::class, 'info']);
    Route::post('/add', [ExperienceController::class, 'store']);
    Route::prefix('{experience}')->group(function () {
        Route::get('/', [ExperienceController::class, 'show']);
        Route::post('/edit', [ExperienceController::class, 'edit']);
        Route::delete('delete', [ExperienceController::class, 'delete']);
    });
});
