<?php

use App\Http\Controllers\Webhook\MyFatoorahWebhookController;
use Illuminate\Support\Facades\Route;

/**
 * MyFatoorah WebHook
 */
Route::post('myfatoorah', MyFatoorahWebhookController::class);
