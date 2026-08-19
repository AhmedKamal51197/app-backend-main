<?php

use App\Http\Controllers\Admin\Report\ReportController;
use App\Http\Controllers\Admin\Response\ResponseController;
use Illuminate\Support\Facades\Route;

Route::prefix('reports')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->middleware('permission:Index Reports');
    Route::post('/add', [ReportController::class, 'store'])->middleware('permission:Add Reports');
    Route::prefix('{report}')->group(function () {
        Route::get('/', [ReportController::class, 'show'])->middleware('permission:Details Reports');
        Route::post('/toggle-status', [ReportController::class, 'toggleStatus'])->middleware('permission:Toggle Report Status');
        Route::delete('/delete', [ReportController::class, 'destroy'])->middleware('permission:Delete Reports');
        Route::post('/response', [ResponseController::class, 'store'])->middleware('permission:Respond To Reports');
    });
});
