<?php

use Illuminate\Support\Facades\Route;

use App\Http\Middleware\AppMiddleware;

use App\Http\Controllers\Primary\SpaceController;

use App\Http\Controllers\Primary\PlayerController;
use App\Http\Controllers\Primary\PersonController;


// Primary
Route::middleware([
    'auth',
    'verified',
    AppMiddleware::class,
])->group(function () {
    // Spaces
    Route::get('spaces/data', [SpaceController::class, 'getSpacesData'])->name('spaces.data');
    Route::post('/spaces/switch/{code}', [SpaceController::class, 'switchSpace'])->name('spaces.switch');
    Route::get('spaces/exit/{route}', [SpaceController::class, 'exitSpace'])->name('spaces.exit');
    Route::get('spaces/exit', [SpaceController::class, 'exitSpace'])->name('spaces.exit');
    Route::resource('spaces', SpaceController::class);


    // Players
    Route::get('players/data', [PlayerController::class, 'getPlayersData'])->name('players.data');
    Route::resource('players', PlayerController::class);
    
    Route::get('persons/data', [PersonController::class, 'getPersonsData'])->name('persons.data');
    Route::resource('persons', PersonController::class);
});