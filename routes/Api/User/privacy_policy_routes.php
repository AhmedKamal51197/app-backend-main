<?php

use App\Http\Controllers\Api\PrivacyPolicy\PrivacyPolicyController;
use Illuminate\Support\Facades\Route;

Route::prefix('privacy-policy')->middleware('auth:api')->group(callback: function () {
    Route::get('/', [PrivacyPolicyController::class, 'privacyPolicy']);
});
