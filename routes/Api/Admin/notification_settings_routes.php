<?php

use App\Http\Controllers\Admin\NotificationSetting\NotificationSettingController;
use Illuminate\Support\Facades\Route;

Route::prefix('notification-settings')->group(function () {
    Route::get('/', [NotificationSettingController::class, 'index'])->middleware('permission:Index Notification Settings');
    Route::post('/{notificationSetting}/toggle-active', [NotificationSettingController::class, 'toggleStatus'])->middleware('permission:Edit Notification Settings');
});