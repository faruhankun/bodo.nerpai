<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Primary\SpaceController;


Route::middleware([
    'auth:sanctum',
])->group(function () {
    Route::get('spaces/data', [SpaceController::class, 'getSpacesDT'])->name('spaces.data');
    Route::resource('spaces', SpaceController::class);
});


Route::middleware([
])->group(function () {
    Route::get('spaces/search', [SpaceController::class, 'search'])->name('spaces.search');
});
