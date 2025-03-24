<?php 

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CompanyMiddleware;

use App\Http\Controllers\Company\WarehouseController;
use App\Http\Controllers\Warehouse\WarehouseInventoryController;
use App\Http\Controllers\Warehouse\InboundController;
use App\Http\Controllers\Warehouse\OutboundController;

Route::middleware([
    'auth',
    CompanyMiddleware::class,
])->group(function () {
    // Store
    Route::get('/dashboard-warehouse', function () { return view('warehouse.dashboard-warehouse');})->name('dashboard-warehouse');

    Route::resource('warehouse_settings', WarehouseController::class);

    Route::resource('warehouse_employees', WarehouseController::class);
    
    Route::resource('warehouse_roles', WarehouseController::class);
    Route::resource('warehouse_permissions', WarehouseController::class);

    Route::get('/warehouse_inventories/movement_index', [WarehouseInventoryController::class, 'movement_index'])->name('warehouse_inventories.movement_index');
    Route::resource('warehouse_inventories', WarehouseInventoryController::class);

    Route::resource('warehouse_inbounds', InboundController::class);
    Route::post('warehouse_inbounds/{warehouse_inbounds}/action/{action}', [InboundController::class, 'handleAction'])->name('warehouse_inbounds.action');
    
    Route::resource('warehouse_outbounds', OutboundController::class);
    Route::post('warehouse_outbounds/{warehouse_outbounds}/action/{action}', [OutboundController::class, 'handleAction'])->name('warehouse_outbounds.action');  
});