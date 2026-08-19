<?php

use App\Http\Controllers\Api\Report\ReportController;
use App\Http\Controllers\Api\Response\ResponseController;
use Illuminate\Support\Facades\Route;

// Report routes
Route::prefix('reports')->middleware('auth:api')->group(function () {
    Route::get('/', [ReportController::class, 'index']);
    Route::post('/add', [ReportController::class, 'store']);
    Route::prefix('{report}')->group(function () {
        Route::get('/', [ReportController::class, 'show']);
        Route::post('/close', [ReportController::class, 'close']);
        Route::delete('/delete', [ReportController::class, 'destroy']);
        Route::post('/response', [ResponseController::class, 'store']);
    });
});
