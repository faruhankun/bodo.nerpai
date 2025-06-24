<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Primary\Inventory\AccountController;

Route::middleware([
])->group(function () {
    // Account
    Route::get('accounts/data', [AccountController::class, 'getAccountsData'])->name('accounts.data');
});