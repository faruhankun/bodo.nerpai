<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Primary\Inventory\AccountController;
use App\Http\Controllers\Primary\Inventory\InventoryController;



Route::middleware([
])->group(function () {
    Route::prefix('supplies')->controller(InventoryController::class)->group(function () {
        Route::get('/data', 'getData');
        Route::get('/import', 'importTemplate');
        Route::post('/import', 'importData');
        Route::get('/export', 'exportData');
    });

    Route::prefix('accounts')->controller(AccountController::class)->group(function () {
        Route::get('/import', 'importTemplate');
        Route::post('/import', 'importData');
        Route::get('/export', 'exportData');

        Route::get('/summary', 'summary');
        Route::get('/transactions', 'getAccountTransactions');
    });
});



Route::middleware([
    // 'auth:sanctum',
])->group(function () {
    // Account
    Route::get('accounts/search', [AccountController::class, 'search'])->name('accounts.search');
    Route::get('accounts/types/data', [AccountController::class, 'getAccountTypesData'])->name('accounts.types.data');
    Route::get('accounts/data', [AccountController::class, 'getAccountsData'])->name('accounts.data');
    Route::resource('accounts', AccountController::class);



    // Supplies
    Route::resource('supplies', InventoryController::class);
});