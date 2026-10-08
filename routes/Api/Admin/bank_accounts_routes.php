<?php

use App\Http\Controllers\Admin\BankAccount\BankAccountController;
use Illuminate\Support\Facades\Route;

// Admin: view and export users' bank accounts (for manual payouts).
Route::prefix('bank-accounts')->group(function () {
    Route::get('/', [BankAccountController::class, 'index'])
        ->middleware('permission:Index Payment Requests');

    Route::get('/export', [BankAccountController::class, 'export'])
        ->middleware('permission:Index Payment Requests');

    Route::get('/withdrawals/export', [BankAccountController::class, 'exportWithdrawals'])
        ->middleware('permission:Index Payment Requests');
});
