<?php

use App\Http\Controllers\Admin\Wallet\WalletController;
use Illuminate\Support\Facades\Route;

Route::prefix('wallets')->group(function () {
    Route::get('/', [WalletController::class, 'index'])->middleware('permission:Index Wallets');
    Route::get('/{wallet}/receipt', [WalletController::class, 'getReceipt'])->middleware('permission:Details Wallets');
});
