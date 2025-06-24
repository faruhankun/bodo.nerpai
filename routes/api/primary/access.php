<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Primary\Access\VariableController;

Route::middleware([
])->group(function () {
    Route::get('variables/get', [VariableController::class, 'getVariable'])->name('variables.get');
    Route::get('variables/data', [VariableController::class, 'getVariablesData'])->name('variables.data');
});