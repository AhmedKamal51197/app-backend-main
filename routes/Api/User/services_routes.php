<?php

use App\Http\Controllers\Api\Service\ServiceController;
use Illuminate\Support\Facades\Route;

// "Services for you": public (guest mode) with an optional token; guests and
// users without interests get the approved services.
Route::get('services/interests', [ServiceController::class, 'indexInterests'])->middleware('auth.optional');

// Service details are public too (a guest opens a card from the home page).
// whereUuid keeps fixed paths like /my-services out of this route.
Route::get('services/{service}', [ServiceController::class, 'show'])
    ->whereUuid('service')
    ->middleware('auth.optional');

Route::prefix('services')->middleware('auth:api')->group(callback: function () {
    Route::get('/', [ServiceController::class, 'index']); 
    Route::get('/my-services', [ServiceController::class, 'indexUserServices']);
    Route::post('/add', [ServiceController::class, 'store']);
    Route::prefix('{service}')->group(function () {
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
