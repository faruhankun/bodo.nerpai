<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Primary\SpaceController;


Route::middleware([
    'auth:sanctum',
])->group(function () {
    Route::get('spaces/data', [SpaceController::class, 'getSpacesDT'])->name('spaces.data');
});


Route::middleware([
])->group(function () {
    Route::get('spaces/search', [SpaceController::class, 'search'])->name('spaces.search');
    Route::resource('spaces', SpaceController::class);
});
