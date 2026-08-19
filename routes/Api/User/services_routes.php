<?php

use App\Http\Controllers\Api\Service\ServiceController;
use Illuminate\Support\Facades\Route;

Route::prefix('services')->middleware('auth:api')->group(callback: function () {
    Route::get('/', [ServiceController::class, 'index']); 
    Route::get('/my-services', [ServiceController::class, 'indexUserServices']);
    Route::get('/interests', [ServiceController::class, 'indexInterests']);
    Route::post('/add', [ServiceController::class, 'store']);
    Route::prefix('{service}')->group(function () {
        Route::get('/', [ServiceController::class, 'show']);
        Route::post('/edit', [ServiceController::class, 'edit']);
        Route::delete('delete', [ServiceController::class, 'delete']);
        Route::prefix('attachments')->group(callback: function () {
            Route::post('/add', [ServiceController::class, 'addAttachment']);
            Route::prefix('{attachment}')->group(function () {
                Route::delete('delete', [ServiceController::class, 'deleteAttachment']);
            });
        });
        Route::prefix('packages')->group(callback: function () {
            Route::prefix('{package}')->group(function () {
                Route::post('edit', [ServiceController::class, 'editPackage']);
            });
        });
        Route::prefix('skills')->group(callback: function () {
            Route::post('/add', [ServiceController::class, 'addSkills']);
            Route::post('delete', [ServiceController::class, 'removeSkills']);
        });
    });
});
