<?php

use Illuminate\Support\Facades\Route;

use App\Http\Middleware\AppMiddleware;
use App\Http\Middleware\Space\SpaceMiddleware;

use App\Http\Controllers\Primary\SpaceController;

use App\Http\Controllers\Primary\PlayerController;
use App\Http\Controllers\Space\Player\SpacePlayerController;


// Primary
Route::middleware([
    'auth',
    'verified',
    AppMiddleware::class,
    SpaceMiddleware::class,
])->group(function () {
    // Spaces
    Route::get('spaces/data', [SpaceController::class, 'getSpacesData'])->name('spaces.data');
    Route::post('/spaces/switch/{code}', [SpaceController::class, 'switchSpace'])->name('spaces.switch');
    Route::get('spaces/exit/{route}', [SpaceController::class, 'exitSpace'])->name('spaces.exit');
    Route::get('spaces/exit', [SpaceController::class, 'exitSpace'])->name('spaces.exit');
    Route::resource('spaces', SpaceController::class);


    // Players
    Route::get('space_players/data', [SpacePlayerController::class, 'getSpacePlayersData'])->name('space_players.data');
    Route::get('space_players/search', [SpacePlayerController::class, 'search'])->name('space_players.search');
    Route::resource('space_players', SpacePlayerController::class);

});