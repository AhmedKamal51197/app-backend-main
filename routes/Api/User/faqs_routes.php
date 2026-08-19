<?php

use App\Http\Controllers\Api\Faq\FaqController;
use Illuminate\Support\Facades\Route;

Route::prefix('faqs')->group(function () {
    Route::get('/', [FaqController::class, 'index']);
    Route::get('{faq}', [FaqController::class, 'show']);
});
