<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Primary\Transaction\JournalAccountController;



Route::middleware([
])->group(function () {
    Route::prefix('journal_accounts')->controller(JournalAccountController::class)->group(function () {
        Route::get('/data', 'getDataTable');
    });

    Route::get('journal_accounts/export', [JournalAccountController::class, 'exportData'])->name('journal_accounts.export');
    Route::get('journal_accounts/import', [JournalAccountController::class, 'importTemplate'])->name('journal_accounts.import_template');
    Route::post('journal_accounts/import', [JournalAccountController::class, 'importData'])->name('journal_accounts.import');
});




Route::middleware([
    'auth:sanctum',
])->group(function () {
    Route::resource('journal_accounts', JournalAccountController::class);
});