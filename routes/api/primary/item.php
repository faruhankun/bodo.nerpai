<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Primary\ItemController;



Route::middleware([
])->group(function () {
    Route::prefix('items')->controller(ItemController::class)->group(function () {
        Route::get('/data', 'getData');
        Route::get('/import', 'importTemplate');
        Route::post('/import', 'importData');
        Route::get('/export', 'exportData');
    });
});




Route::middleware([
    'auth:sanctum',
])->group(function () {
    Route::resource('items', ItemController::class);
});