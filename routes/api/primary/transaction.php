<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Primary\Transaction\JournalAccountController;
use App\Http\Controllers\Primary\Transaction\JournalSupplyController;



Route::middleware([
])->group(function () {
    Route::prefix('journal_accounts')->controller(JournalAccountController::class)->group(function () {
        Route::get('/data', 'getDataTable');
    });

    Route::get('journal_accounts/export', [JournalAccountController::class, 'exportData'])->name('journal_accounts.export');
    Route::get('journal_accounts/import', [JournalAccountController::class, 'importTemplate'])->name('journal_accounts.import_template');
    Route::post('journal_accounts/import', [JournalAccountController::class, 'importData'])->name('journal_accounts.import');



    // jurnal supplies
    Route::prefix('journal_supplies')->controller(JournalSupplyController::class)->group(function () {
        Route::get('/data', 'getDataTable');
        Route::get('/import', 'importTemplate');
        Route::post('/import', 'importData');
        Route::get('/export', 'exportData');
    });
});




Route::middleware([
    'auth:sanctum',
])->group(function () {
    Route::resource('journal_accounts', JournalAccountController::class);
    Route::resource('journal_supplies', JournalSupplyController::class);
});