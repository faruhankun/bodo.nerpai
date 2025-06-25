<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Primary\Inventory\AccountController;


Route::middleware([
])->group(function () {

});

Route::middleware([
    'auth:sanctum',
])->group(function () {
    // Account
    Route::get('accounts/search', [AccountController::class, 'search'])->name('accounts.search');
    Route::get('accounts/types/data', [AccountController::class, 'getAccountTypesData'])->name('accounts.types.data');
    Route::get('accounts/data', [AccountController::class, 'getAccountsData'])->name('accounts.data');
    Route::resource('accounts', AccountController::class);
});