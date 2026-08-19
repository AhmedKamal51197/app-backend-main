<?php

use App\Http\Controllers\Admin\Order\CancelOrderController;
use App\Http\Controllers\Admin\Order\OrderController;
use App\Http\Controllers\Admin\Order\RefundOrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('orders')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->middleware('permission:Index Orders');
    Route::get('/pending', [OrderController::class, 'pending'])->middleware('permission:Index Orders');
    Route::get('/rejected', [OrderController::class, 'rejected'])->middleware('permission:Index Orders');
    Route::get('/in-progress', [OrderController::class, 'inProgress'])->middleware('permission:Index Orders');
    Route::get('/released', [OrderController::class, 'released'])->middleware('permission:Index Orders');
    Route::get('/refunded', [OrderController::class, 'refunded'])->middleware('permission:Index Orders');
    Route::get('/completed', [OrderController::class, 'completed'])->middleware('permission:Index Orders');
    Route::get('/cancelled', [OrderController::class, 'cancelled'])->middleware('permission:Index Orders');
    Route::get('/disputed', [OrderController::class, 'disputed'])->middleware('permission:Index Orders');
    Route::prefix('{order}')->group(function () {
        Route::get('/', [OrderController::class, 'show'])->middleware('permission:Details Orders');
        Route::post('/cancel', CancelOrderController::class)->middleware('permission:Cancel Orders');
        Route::post('/refund', RefundOrderController::class)->middleware('permission:Refund Orders');
        Route::post('/approve-cancellation', [OrderController::class, 'approveCancellation'])->middleware('permission:Approve Order Cancellation');
        Route::post('/reject-cancellation', [OrderController::class, 'rejectCancellation'])->middleware('permission:Reject Order Cancellation');
        Route::delete('/delete', [OrderController::class, 'delete']);
    });
});
