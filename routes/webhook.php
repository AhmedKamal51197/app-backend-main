<?php

use App\Http\Controllers\Webhook\MyFatoorahWebhookController;
use App\Http\Controllers\Webhook\TapReturnController;
use App\Http\Controllers\Webhook\TapWebhookController;
use Illuminate\Support\Facades\Route;

/**
 * MyFatoorah WebHook
 */
Route::post('myfatoorah', MyFatoorahWebhookController::class);

/**
 * Tap WebHook (authenticated by the hashstring signature) and customer return URL
 */
Route::post('tap', TapWebhookController::class)->middleware('throttle:120,1')->name('tap.webhook');
Route::get('tap/return', TapReturnController::class)->middleware('throttle:60,1')->name('tap.return');
