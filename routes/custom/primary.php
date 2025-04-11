<?php

use Illuminate\Support\Facades\Route;

use App\Http\Middleware\AppMiddleware;

use App\Http\Controllers\Primary\PlayerController;
use App\Http\Controllers\Primary\PersonController;


// Primary
Route::middleware([
    'auth',
    'verified',
    AppMiddleware::class,
])->group(function () {
    // Players
    Route::get('players/data', [PlayerController::class, 'getPlayersData'])->name('players.data');
    Route::resource('players', PlayerController::class);
    
    Route::get('persons/data', [PersonController::class, 'getPersonsData'])->name('persons.data');
    Route::resource('persons', PersonController::class);
});