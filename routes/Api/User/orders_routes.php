<?php

use App\Http\Controllers\Api\Order\ApproveOrderController;
use App\Http\Controllers\Api\Order\DisputeOrderController;
use App\Http\Controllers\Api\Order\OrderController;
use App\Http\Controllers\Api\Order\OrderMessagesController;
use App\Http\Controllers\Api\Order\RateOrderController;
use App\Http\Controllers\Api\Order\RejectOrderController;
use App\Http\Controllers\Api\Order\ReleaseOrderController;
use App\Http\Controllers\Api\Order\RequestCancelOrderController;
use App\Http\Controllers\Api\Order\RequestReleaseOrderController;
use App\Http\Controllers\Api\Order\RequestRevisionOrderController;
use App\Http\Controllers\Api\Order\SeekerOrdersController;
use Illuminate\Support\Facades\Route;

Route::prefix('orders')->middleware('auth:api')->group(callback: function () {
    Route::get('/', [OrderController::class, 'index']);
    Route::get('/seeker', [SeekerOrdersController::class, 'index']);
    Route::get('/one-time', [SeekerOrdersController::class, 'one_time_services']);
    Route::get('/part-time', [SeekerOrdersController::class, 'part_time_services']);
    Route::prefix('{order}')->group(function () {
        Route::get('/', [OrderController::class, 'show']);
        Route::get('/part-time', [OrderController::class, 'showPartTime']);
        Route::post('/approve', ApproveOrderController::class);
        Route::post('/reject', RejectOrderController::class);
        Route::post('/cancel-request', RequestCancelOrderController::class);
        Route::post('/dispute', DisputeOrderController::class);
        Route::post('/request-revision', RequestRevisionOrderController::class);
        Route::post('/release-request', RequestReleaseOrderController::class);
        Route::post('/release', ReleaseOrderController::class);
        Route::post('/rate', RateOrderController::class);
        Route::post('/messages/send', [OrderMessagesController::class, 'store']);
        Route::get('/history', [OrderController::class, 'history']);
    });
});
