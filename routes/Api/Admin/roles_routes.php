<?php

use App\Http\Controllers\Admin\Role\RoleController;
use Illuminate\Support\Facades\Route;

Route::prefix('roles')->group(callback: function () {
    Route::get('/', [RoleController::class, 'index'])->middleware('permission:Index Roles');
    Route::post('/add', [RoleController::class, 'store'])->middleware('permission:Add Roles');
    Route::prefix('supervisor-users')->group(function () {
        Route::get('/', [RoleController::class, 'supervisorUsers']);
        Route::post('/add', [RoleController::class, 'createSupervisor']);
        Route::get('/{user}', [RoleController::class, 'showSupervisorUser']);
        Route::post('/{user}/edit', [RoleController::class, 'editSupervisor']);
        Route::post('/{user}/update-password', [RoleController::class, 'updateSupervisorPassword']);
    });
    Route::prefix('{role}')->group(function () {
        Route::get('/', [RoleController::class, 'show'])->middleware('permission:Details Roles');
        Route::post('/edit', [RoleController::class, 'update'])->middleware('permission:Edit Roles');
        Route::post('/toggle-status', [RoleController::class, 'toggleActive'])->middleware('permission:Edit Roles');
        Route::delete('/delete', [RoleController::class, 'destroy'])->middleware('permission:Delete Roles');
        Route::prefix('permissions')->group(function () {
            Route::prefix('{permission}')->group(function () {
                Route::post('add', [RoleController::class, 'addPermission'])->middleware('permission:Add Roles Permissions');
                Route::delete('delete', [RoleController::class, 'deletePermission'])->middleware('permission:Delete Roles Permissions');
            });
        });
    });
});
