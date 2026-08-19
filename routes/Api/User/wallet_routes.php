<?php

use App\Http\Controllers\Api\Wallet\WalletController;
use Illuminate\Support\Facades\Route;

Route::prefix('wallets')->middleware('auth:api')->group(callback: function () {
    Route::get('/', [WalletController::class, 'index']);
    Route::get('/getbalance', [WalletController::class, 'getbalance']);
});
