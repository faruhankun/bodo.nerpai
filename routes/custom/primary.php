<?php

use Illuminate\Support\Facades\Route;

use App\Http\Middleware\AppMiddleware;
use App\Http\Middleware\Space\SpaceMiddleware;

use App\Http\Controllers\Primary\SpaceController;

use App\Http\Controllers\Primary\PlayerController;
use App\Http\Controllers\Primary\Player\ContactController;
use App\Http\Controllers\Primary\Player\TeamController;

use App\Http\Controllers\Primary\PersonController;
use App\Http\Controllers\Primary\GroupController;

use App\Http\Controllers\Primary\ItemController;
use App\Http\Controllers\Primary\Inventory\InventoryController;
use App\Http\Controllers\Primary\Inventory\AccountController;

use App\Http\Controllers\Primary\Transaction\QuoteController;
use App\Http\Controllers\Primary\Transaction\TradeController;
use App\Http\Controllers\Primary\Transaction\JournalSupplyController;
use App\Http\Controllers\Primary\Transaction\JournalAccountController;

use App\Http\Controllers\Primary\Access\VariableController;
use App\Http\Controllers\Primary\Access\PermissionController;
use App\Http\Controllers\Primary\Access\RoleController;

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
    Route::post('quotes/make_trade/{id}', [QuoteController::class, 'makeTrade'])->name('quotes.make_trade');
    Route::post('quotes/duplicate/{id}', [QuoteController::class, 'duplicate'])->name('quotes.duplicate');
    Route::get('quotes/{id}/invoice', [QuoteController::class, 'invoice'])->name('quotes.invoice');

    Route::post('quotes/exim', [QuoteController::class, 'eximData'])->name('quotes.exim');
    Route::get('quotes/exim', [QuoteController::class, 'eximData'])->name('quotes.exim');
    Route::get('quotes/data', [QuoteController::class, 'getData'])->name('quotes.data');
    Route::resource('quotes', QuoteController::class);


    Route::post('trades/duplicate/{id}', [TradeController::class, 'duplicate'])->name('trades.duplicate');
    Route::get('trades/{id}/invoice', [TradeController::class, 'invoice'])->name('trades.invoice');

    Route::post('trades/exim', [TradeController::class, 'eximData'])->name('trades.exim');
    Route::get('trades/exim', [TradeController::class, 'eximData'])->name('trades.exim');
    Route::get('trades/data', [TradeController::class, 'getData'])->name('trades.data');
    Route::resource('trades', TradeController::class);


    Route::post('journal_supplies/request_trade/{id}', [JournalSupplyController::class, 'requestTrade'])->name('journal_supplies.request_trade');
    Route::get('journal_supplies/import', [JournalSupplyController::class, 'importTemplate'])->name('journal_supplies.import_template');
    Route::post('journal_supplies/import', [JournalSupplyController::class, 'importData'])->name('journal_supplies.import');
    Route::get('journal_supplies/export', [JournalSupplyController::class, 'exportData'])->name('journal_supplies.export');
    Route::get('journal_supplies/data', [JournalSupplyController::class, 'getData'])->name('journal_supplies.data');
    Route::resource('journal_supplies', JournalSupplyController::class);

    Route::get('journal_accounts/export', [JournalAccountController::class, 'exportData'])->name('journal_accounts.export');
    Route::get('journal_accounts/import', [JournalAccountController::class, 'importTemplate'])->name('journal_accounts.import_template');
    Route::post('journal_accounts/import', [JournalAccountController::class, 'importData'])->name('journal_accounts.import');
    Route::get('journal_accounts/data', [JournalAccountController::class, 'getData'])->name('journal_accounts.data');
    Route::resource('journal_accounts', JournalAccountController::class);



    // Items
    Route::get('items/summary', [ItemController::class, 'summary'])->name('items.summary');
    Route::post('items/import', [ItemController::class, 'importData'])->name('items.import');
    Route::get('items/import', [ItemController::class, 'importTemplate'])->name('items.importtemplate');
    Route::get('items/export', [ItemController::class, 'exportData'])->name('items.export');
    Route::get('items/search', [ItemController::class, 'searchItem'])->name('items.search');
    Route::get('items/data', [ItemController::class, 'getItemsData'])->name('items.data');
    Route::resource('items', ItemController::class);



    // Supplies
    Route::get('supplies/report', function () { return view('primary.inventory.supplies.page'); })->name('supplies.report');
    Route::get('supplies/supply_transactions', [InventoryController::class, 'getSupplyTransactions'])->name('supplies.supply_transactions');
    
    Route::get('supplies/summary', [InventoryController::class, 'summary'])->name('supplies.summary');
    Route::post('supplies/import', [InventoryController::class, 'importData'])->name('supplies.import');
    Route::get('supplies/import', [InventoryController::class, 'importTemplate'])->name('supplies.import_template');
    Route::get('supplies/export', [InventoryController::class, 'exportData'])->name('supplies.export');
    Route::get('supplies/search', [InventoryController::class, 'searchSupply'])->name('supplies.search');
    Route::get('supplies/data', [InventoryController::class, 'getSuppliesData'])->name('supplies.data');
    Route::resource('supplies', InventoryController::class);


    // Accounts
    Route::get('accounts/tree', [AccountController::class, 'tree'])->name('accounts.tree');
    Route::get('accounts/jstree', [AccountController::class, 'jstree'])->name('accounts.jstree');
    Route::get('accountsp/account_transactions', [AccountController::class, 'getAccountTransactions'])->name('accountsp.account_transactions');

    Route::post('accountsp/import', [AccountController::class, 'importData'])->name('accountsp.import');
    Route::get('accountsp/import', [AccountController::class, 'importTemplate'])->name('accountsp.import_template');
    Route::get('accountsp/export', [AccountController::class, 'exportData'])->name('accountsp.export');
    Route::get('accountsp/summary', [AccountController::class, 'summary'])->name('accountsp.summary');
    Route::get('accountsp/data', [AccountController::class, 'getAccountsData'])->name('accountsp.data');
    Route::resource('accountsp', AccountController::class);



    // Contacts
    Route::post('contacts/exim', [ContactController::class, 'eximData'])->name('contacts.exim');
    Route::get('contacts/exim', [ContactController::class, 'eximData'])->name('contacts.exim');

    Route::get('contacts/summary', [ContactController::class, 'summary'])->name('contacts.summary');
    Route::get('contacts/data', [ContactController::class, 'getContactsData'])->name('contacts.data');
    Route::resource('contacts', ContactController::class);



    // Teams
    Route::get('teams/data', [TeamController::class, 'getData'])->name('teams.data');
    Route::resource('teams', TeamController::class);


    
    // Players
    Route::get('players/summary', [PlayerController::class, 'summary'])->name('players.summary');
    Route::post('players/exim', [PlayerController::class, 'eximData'])->name('players.exim');
    Route::get('players/exim', [PlayerController::class, 'eximData'])->name('players.exim');

    Route::post('/players/switch/{id}', [PlayerController::class, 'switchPlayer'])->name('players.switch');
    Route::get('players/exit/{route}', [PlayerController::class, 'exitPlayer'])->name('players.exit');
    Route::get('players/exit', [PlayerController::class, 'exitPlayer'])->name('players.exit');
    Route::get('players/related', [PlayerController::class, 'getRelatedPlayersData'])->name('players.related');
    Route::post('players/related', [PlayerController::class, 'storeRelatedPlayer'])->name('players.related.store');
    Route::put('players/related/{id}', [PlayerController::class, 'updateRelatedPlayer'])->name('players.related.update');
    Route::get('players/search', [PlayerController::class, 'searchPlayer'])->name('players.search');

    Route::get('players/data', [PlayerController::class, 'getData'])->name('players.data');
    Route::resource('players', PlayerController::class);

    
    Route::get('persons/data', [PersonController::class, 'getPersonsData'])->name('persons.data');
    Route::resource('persons', PersonController::class);

    Route::get('groups/data', [GroupController::class, 'getGroupsData'])->name('groups.data');
    Route::resource('groups', GroupController::class);



    // Access
    Route::get('variables/data', [VariableController::class, 'getVariablesData'])->name('variables.data');
    Route::resource('variables', VariableController::class);

    Route::get('roles/data', [RoleController::class, 'getData'])->name('roles.data');
    Route::resource('roles', RoleController::class);

    Route::get('skills/data', [PermissionController::class, 'getData'])->name('skills.data');
    Route::resource('skills', PermissionController::class);


    // Reports
    Route::resource("summaries", ReportController::class);
});
