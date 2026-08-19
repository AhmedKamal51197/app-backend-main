<?php

use App\Http\Controllers\Admin\Faq\FaqController;
use Illuminate\Support\Facades\Route;

Route::prefix('faqs')->group(function () {
    Route::get('/', [FaqController::class, 'index'])->middleware('permission:Index FAQs');
    Route::post('/add', [FaqController::class, 'store'])->middleware('permission:Add FAQs');
    Route::prefix('/{faq}')->group(function () {
        Route::get('/', [FaqController::class, 'show'])->middleware('permission:Details FAQs');
        Route::post('/edit', [FaqController::class, 'update'])->middleware('permission:Edit FAQs');
        Route::delete('/delete', [FaqController::class, 'destroy'])->middleware('permission:Delete FAQs');
        Route::post('/toggle-active', [FaqController::class, 'toggleActive'])->middleware('permission:Toggle FAQ Status');
    });
});