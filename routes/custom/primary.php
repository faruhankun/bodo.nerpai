<?php

use Illuminate\Support\Facades\Route;

use App\Http\Middleware\AppMiddleware;
use App\Http\Middleware\Space\SpaceMiddleware;

use App\Http\Controllers\Primary\SpaceController;

use App\Http\Controllers\Primary\PlayerController;
use App\Http\Controllers\Primary\PersonController;
use App\Http\Controllers\Primary\GroupController;

use App\Http\Controllers\Primary\ItemController;
use App\Http\Controllers\Primary\Inventory\InventoryController;
use App\Http\Controllers\Primary\Inventory\AccountController;

use App\Http\Controllers\Primary\Transaction\TradeController;
use App\Http\Controllers\Primary\Transaction\JournalSupplyController;
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
    Route::get('spaces/exit', [SpaceController::class, 'exitSpace'])->name('spaces.exit');
    Route::get('spaces/exit/{route}', [SpaceController::class, 'exitSpace'])->name('spaces.exit');
    Route::resource('spaces', SpaceController::class);



    // Transactions
    Route::get('trades/po/data', [TradeController::class, 'getTradesPOData'])->name('trades.po.data');
    Route::get('trades/po', [TradeController::class, 'indexPO'])->name('trades.po');
    Route::get('trades/so/data', [TradeController::class, 'getTradesSOData'])->name('trades.so.data');
    Route::get('trades/so', [TradeController::class, 'indexSO'])->name('trades.so');
    Route::get('trades/data', [TradeController::class, 'getTradesData'])->name('trades.data');
    Route::resource('trades', TradeController::class);

    Route::get('journal_supplies/data', [JournalSupplyController::class, 'getJournalSuppliesData'])->name('journal_supplies.data');
    Route::get('journal_supplies/template', [JournalSupplyController::class, 'downloadTemplate'])->name('journal_supplies.template');
    Route::post('journal_supplies/import', [JournalSupplyController::class, 'readCsv'])->name('journal_supplies.import');
    Route::resource('journal_supplies', JournalSupplyController::class);

    Route::get('journal_accounts/data', [JournalAccountController::class, 'getJournalAccountsData'])->name('journal_accounts.data');
    Route::get('journal_accounts/template', [JournalAccountController::class, 'downloadTemplate'])->name('journal_accounts.template');
    Route::post('journal_accounts/import', [JournalAccountController::class, 'readCsv'])->name('journal_accounts.import');
    Route::resource('journal_accounts', JournalAccountController::class);


    // Inventory
    Route::get('items/search', [ItemController::class, 'searchItem'])->name('items.search');
    Route::get('items/data', [ItemController::class, 'getItemsData'])->name('items.data');
    Route::resource('items', ItemController::class);

    Route::get('supplies/search', [InventoryController::class, 'searchSupply'])->name('supplies.search');
    Route::get('supplies/data', [InventoryController::class, 'getSuppliesData'])->name('supplies.data');
    Route::resource('supplies', InventoryController::class);

    Route::get('accountsp/data', [AccountController::class, 'getAccountsData'])->name('accountsp.data');
    Route::resource('accountsp', AccountController::class);



    // Players
    Route::post('/players/switch/{id}', [PlayerController::class, 'switchPlayer'])->name('players.switch');
    Route::get('players/exit/{route}', [PlayerController::class, 'exitPlayer'])->name('players.exit');
    Route::get('players/exit', [PlayerController::class, 'exitPlayer'])->name('players.exit');
    Route::get('players/related', [PlayerController::class, 'getRelatedPlayersData'])->name('players.related');
    Route::post('players/related', [PlayerController::class, 'storeRelatedPlayer'])->name('players.related.store');
    Route::put('players/related/{id}', [PlayerController::class, 'updateRelatedPlayer'])->name('players.related.update');
    Route::get('players/search', [PlayerController::class, 'searchPlayer'])->name('players.search');
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
