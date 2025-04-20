<?php

use Illuminate\Support\Facades\Route;

use App\Http\Middleware\AppMiddleware;

use App\Http\Controllers\Primary\SpaceController;

use App\Http\Controllers\Primary\PlayerController;
use App\Http\Controllers\Primary\PersonController;
use App\Http\Controllers\Primary\GroupController;

use App\Http\Controllers\Primary\ItemController;


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



    // Inventory
    Route::get('items/data', [ItemController::class, 'getItemsData'])->name('items.data');
    Route::resource('items', ItemController::class);



    // Players
    Route::get('players/data', [PlayerController::class, 'getPlayersData'])->name('players.data');
    Route::resource('players', PlayerController::class);
    
    Route::get('persons/data', [PersonController::class, 'getPersonsData'])->name('persons.data');
    Route::resource('persons', PersonController::class);

    Route::get('groups/data', [GroupController::class, 'getGroupsData'])->name('groups.data');
    Route::resource('groups', GroupController::class);
});