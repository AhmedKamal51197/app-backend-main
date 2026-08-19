<?php

use App\Http\Controllers\Api\Job\JobController;
use Illuminate\Support\Facades\Route;

Route::prefix('jobs')->middleware('auth:api')->group(callback: function () {
    Route::get('/', [JobController::class, 'index']);
    Route::post('/add', [JobController::class, 'store']);
    Route::prefix('{job}')->group(function () {
        Route::get('/', [JobController::class, 'show']);
        Route::post('/edit', [JobController::class, 'edit']);
        Route::delete('delete', [JobController::class, 'delete']);
        Route::prefix('attachments')->group(callback: function () {
            Route::post('/add', [JobController::class, 'addAttachment']);
            Route::prefix('{attachment}')->group(function () {
                Route::delete('delete', [JobController::class, 'deleteAttachment']);
            });
        });
        Route::prefix('skills')->group(callback: function () {
            Route::post('/add', [JobController::class, 'addSkills']);
            Route::post('delete', [JobController::class, 'removeSkills']);
        });
    });
});
