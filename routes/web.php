<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Space\ProfileController;

use App\Http\Controllers\Space\UserController;

use App\Http\Controllers\Space\RoleController;
use App\Http\Controllers\Space\PermissionController;

use App\Http\Controllers\Space\CompanyController;

use App\Http\Controllers\Company\StoreController;

use App\Http\Controllers\Company\WarehouseController;

use App\Http\Controllers\Company\Client\MigrateClientController;

use App\Http\Middleware\AppMiddleware;
use App\Http\Middleware\CompanyMiddleware;
use App\Http\Middleware\Space\SpaceMiddleware;

Route::get('/', function () {
    return view('welcome');
});


// Lobby
Route::middleware([
    'auth',
    SpaceMiddleware::class,
])->group(function () {
    // Lobby
    Route::get('/lobby', function () {
        session(['layout' => 'lobby']);
        return view('space.lobby');
    })->name('lobby');

    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
});



// App
Route::middleware([
    'auth',
    'verified',
    AppMiddleware::class,
])->group(function () { 
    Route::resource('users', UserController::class);
    
    Route::resource('companies', CompanyController::class);
    Route::post('/companies/switch/{company}', [CompanyController::class, 'switchCompany'])->name('companies.switch');
    Route::get('/exit-company/{route}', [CompanyController::class, 'exitCompany'])->name('exit.company');
    Route::post('/companies/acceptInvite/{id}', [CompanyController::class, 'acceptInvite'])->name('companies.acceptInvite');
    Route::post('/companies/rejectInvite/{id}', [CompanyController::class, 'rejectInvite'])->name('companies.rejectInvite');

    Route::resource('roles', RoleController::class);
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/data', [RoleController::class, 'getRolesData'])->name('roles.data');

    Route::resource('permissions', PermissionController::class);
});



// Space
Route::middleware([
    'auth',
    'verified',
    AppMiddleware::class,
    SpaceMiddleware::class,
])->group(function () {
    Route::get('/dashboard-space', function () { return view('space.dashboard'); })->name('dashboard_space');
});


// Player
Route::middleware([
    'auth',
    'verified',
    AppMiddleware::class,
])->group(function () {
    Route::get('/dashboard-player', function () { return view('player.dashboard'); })->name('dashboard_player');
});



// Company
Route::middleware(['auth', 
                CompanyMiddleware::class,
])->group(function () {
    Route::get('/dashboard-company', function () { return view('company.dashboard-company'); })->name('dashboard-company');

    // Stores
    Route::resource('stores', StoreController::class);
    
    Route::post('/stores/switch/{stores}', [StoreController::class, 'switchStore'])->name('stores.switch');
    Route::get('/exit-store/{route}', [StoreController::class, 'exitStore'])->name('exit.store');



    // Warehouse
    Route::get('/dashboard-warehouse', function () { return view('company.dashboard-warehouse');})->name('dashboard-warehouse');

    Route::resource('warehouses', WarehouseController::class);

    Route::post('warehouses/switch/{warehouses}', [WarehouseController::class, 'switchWarehouse'])->name('warehouses.switch'); 
    Route::get('warehuses/exit/{route}', [WarehouseController::class, 'exitWarehouse'])->name('warehouses.exit');



    
    // file migrate client
    Route::resource('migrate_client', MigrateClientController::class);
});


// require routes dari custom
foreach (glob(base_path('routes/custom/*.php')) as $file) {
    require $file;
}

require __DIR__ . '/auth.php';
