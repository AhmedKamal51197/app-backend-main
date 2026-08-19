<?php

use App\Http\Controllers\Admin\Certificate\CertificateController;
use Illuminate\Support\Facades\Route;

Route::prefix('certificates')->group(callback: function () {
    Route::prefix('{certificate}')->group(function () {
        Route::get('/', [CertificateController::class, 'show']);
        Route::post('/edit', [CertificateController::class, 'edit']);
        Route::delete('/delete', [CertificateController::class, 'delete']);
    });
});