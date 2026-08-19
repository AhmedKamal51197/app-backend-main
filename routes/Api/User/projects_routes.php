<?php

use App\Http\Controllers\Api\Project\ProjectController;
use Illuminate\Support\Facades\Route;

Route::prefix('projects')->middleware('auth:api')->group(function () {
    Route::get('/', [ProjectController::class, 'index']);
    Route::get('/my-projects', [ProjectController::class, 'indexUserProjects']);
    Route::post('/add', [ProjectController::class, 'store']);
    Route::prefix('{project}')->group(function () {
        Route::get('/', [ProjectController::class, 'show']);
        Route::post('/publish', [ProjectController::class, 'publish']);
        Route::post('/cancel', [ProjectController::class, 'cancel']);
        Route::post('/complete', [ProjectController::class, 'complete']);
        Route::prefix('/proposals/{proposal}')->group(function () {
            Route::post('/accept', [ProjectController::class, 'acceptProposal']);
        });
    });
});
