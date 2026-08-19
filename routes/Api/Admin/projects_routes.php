<?php

use App\Http\Controllers\Admin\Project\ProjectController;
use Illuminate\Support\Facades\Route;

Route::prefix('projects')->group(function () {
    Route::get('/', [ProjectController::class, 'index'])->middleware('permission:Index Projects');
    Route::prefix('{project}')->group(function () {
        Route::get('/', [ProjectController::class, 'show'])->middleware('permission:Details Projects');
        Route::post('/approve-cancellation', [ProjectController::class, 'approveCancellation'])->middleware('permission:Approve Project Cancellation');
        Route::post('/reject-cancellation', [ProjectController::class, 'rejectCancellation'])->middleware('permission:Reject Project Cancellation');
    });
});
