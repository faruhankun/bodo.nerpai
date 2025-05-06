<?php

use Illuminate\Support\Facades\Route;

use App\Http\Middleware\AppMiddleware;
use App\Http\Middleware\Space\SpaceMiddleware;

use App\Http\Controllers\Primary\SpaceController;

use App\Http\Controllers\Primary\PlayerController;
use App\Http\Controllers\Primary\PersonController;
use App\Http\Controllers\Primary\GroupController;

use App\Http\Controllers\Primary\ItemController;
use App\Http\Controllers\Primary\InventoryController;
use App\Http\Controllers\Primary\Inventory\AccountController;

use App\Http\Controllers\Primary\Transaction\JournalAccountController;

use App\Http\Controllers\Primary\Access\VariableController;

use App\Http\Controllers\Primary\Summary\ReportController;

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



    // Transactions
    Route::get('journal_accounts/data', [JournalAccountController::class, 'getJournalAccountsData'])->name('journal_accounts.data');
    Route::post('journal_accounts/import', [JournalAccountController::class, 'readCsv'])->name('journal_accounts.import');
    Route::resource('journal_accounts', JournalAccountController::class);


    // Inventory
    Route::get('items/data', [ItemController::class, 'getItemsData'])->name('items.data');
    Route::resource('items', ItemController::class);

    Route::get('inventories/data', [InventoryController::class, 'getInventoriesData'])->name('inventories.data');
    Route::resource('inventories', InventoryController::class);

    Route::get('accountsp/data', [AccountController::class, 'getAccountsData'])->name('accountsp.data');
    Route::resource('accountsp', AccountController::class);



    // Players
    Route::get('players/data', [PlayerController::class, 'getPlayersData'])->name('players.data');
    Route::resource('players', PlayerController::class);

    Route::get('persons/data', [PersonController::class, 'getPersonsData'])->name('persons.data');
    Route::resource('persons', PersonController::class);

    Route::get('groups/data', [GroupController::class, 'getGroupsData'])->name('groups.data');
    Route::resource('groups', GroupController::class);



    // Access
    Route::get('variables/data', [VariableController::class, 'getVariablesData'])->name('variables.data');
    Route::resource('variables', VariableController::class);


    // Reports
    Route::resource("summaries", ReportController::class);
});
