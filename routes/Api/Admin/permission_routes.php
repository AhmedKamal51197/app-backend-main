<?php

use App\Http\Controllers\Admin\Permission\PermissionController;
use Illuminate\Support\Facades\Route;

Route::prefix('permissions')->group(function () {
    Route::get('/', [PermissionController::class, 'index'])->middleware('permission:Index Permissions');
    Route::post('/add', [PermissionController::class, 'store'])->middleware('permission:Add Permissions');
    Route::prefix('{permission}')->group(function () {
        Route::get('/', [PermissionController::class, 'show'])->middleware('permission:Details Permissions');
        Route::post('/edit', [PermissionController::class, 'update'])->middleware('permission:Edit Permissions');
        Route::delete('/delete', [PermissionController::class, 'delete'])->middleware('permission:Delete Permissions');
    });
});
