<?php

use App\Http\Controllers\Admin\DashboardNotification\DashboardNotificationController;
use Illuminate\Support\Facades\Route;

Route::prefix('dashboard-notifications')->group(function () {
    Route::get('/', [DashboardNotificationController::class, 'index'])->middleware('permission:Index Notifications');
    Route::post('/add', [DashboardNotificationController::class, 'store'])->middleware('permission:Add Notifications');
    Route::get('/{notification:uuid}', [DashboardNotificationController::class, 'show'])->middleware('permission:Details Notifications');
    Route::post('/{notification:uuid}/edit', [DashboardNotificationController::class, 'update'])->middleware('permission:Edit Notifications');
    Route::delete('/{notification:uuid}/delete', [DashboardNotificationController::class, 'destroy'])->middleware('permission:Delete Notifications');
    Route::post('/{notification:uuid}/mark-as-seen', [DashboardNotificationController::class, 'markAsSeen'])->middleware('permission:Edit Notifications');
    Route::post('/{notification:uuid}/mark-as-resolved', [DashboardNotificationController::class, 'markAsResolved'])->middleware('permission:Edit Notifications');
});
