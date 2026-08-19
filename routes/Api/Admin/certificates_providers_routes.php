<?php

use App\Http\Controllers\Admin\Certificate\CertificateController;
use App\Http\Controllers\Admin\CertificateProvider\CertificateProviderController;
use Illuminate\Support\Facades\Route;

Route::prefix('certificate-providers')->group(callback: function () {
    Route::get('/', [CertificateProviderController::class, 'index']);
    Route::post('/add', [CertificateProviderController::class, 'store']);
    Route::prefix('{certificateProvider}')->group(function () {
        Route::get('/', [CertificateProviderController::class, 'show']);
        Route::post('/edit', [CertificateProviderController::class, 'edit']);
        Route::delete('/delete', [CertificateProviderController::class, 'delete']);
        Route::prefix('certificates')->group(callback: function () {
            Route::get('/', [CertificateController::class, 'index']);
            Route::post('/add', [CertificateController::class, 'store']);
        });
    });
});