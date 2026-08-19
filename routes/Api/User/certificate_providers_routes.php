<?php

use App\Http\Controllers\Api\CertificateProvider\CertificateProviderController;
use Illuminate\Support\Facades\Route;

Route::prefix('certificate-providers')->group(function () {
    Route::get('/', [CertificateProviderController::class, 'index']);
    Route::prefix('{certificateProvider}')->group(function () {
        Route::get('/', [CertificateProviderController::class, 'show']);
    });
});
