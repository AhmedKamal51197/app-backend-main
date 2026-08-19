<?php

use App\Http\Controllers\Api\BankAccount\BankAccountController;
use Illuminate\Support\Facades\Route;

// Bank account routes
Route::prefix('bank-account')->middleware('auth:api')->group(function () {
    Route::get('/', [BankAccountController::class, 'userBankAccount']);
    Route::post('/store', [BankAccountController::class, 'store']);
    Route::post('/update', [BankAccountController::class, 'update']);
});
