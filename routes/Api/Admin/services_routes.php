<?php

use App\Http\Controllers\Admin\Project\ProjectController;
use App\Http\Controllers\Admin\Service\ServiceController;
use Illuminate\Support\Facades\Route;

Route::prefix('services')->group(function () {
    Route::get('/', [ServiceController::class, 'index'])->middleware('permission:Index Services');
    Route::prefix('{service}')->group(function () {
        Route::get('/', [ServiceController::class, 'show'])->middleware('permission:Details Services');
        Route::post('/toggle-hidden', [ServiceController::class, 'toggleHidden'])->middleware('permission:Toggle Service Visibility');
        Route::post('/approve', [ServiceController::class, 'approve'])->middleware('permission:Approve Services');
        Route::delete('/delete', [ServiceController::class, 'delete'])->middleware('permission:Delete Services');
    });
});
