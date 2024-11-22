<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CustomerController;

Route::resource('products', ProductController::class);

Route::resource('purchases', PurchaseController::class);
Route::get('purchases/{purchase}/transfer', [PurchaseController::class, 'transferToWarehouse'])->name('purchases.transfer');
Route::post('purchases/{purchase}/transfer', [PurchaseController::class, 'storeTransfer'])->name('purchases.transfer.store');
Route::post('/purchases/{id}/update-status', [PurchaseController::class, 'updateStatus'])->name('purchases.updateStatus');


Route::resource('sales', SalesController::class);
Route::get('sales/{sale}/status/{status}', [SalesController::class, 'updateStatus'])->name('sales.updateStatus');
Route::put('sales/{sale}', [SalesController::class, 'update'])->name('sales.update');


Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
Route::get('/inventory/history', [InventoryController::class, 'history'])->name('inventory.history');
Route::get('inventory/adjust', [InventoryController::class, 'showAdjustmentForm'])->name('inventory.adjustForm');
Route::post('inventory/adjust', [InventoryController::class, 'adjustInventory'])->name('inventory.adjust');
Route::get('/inventory/get-locations/{warehouseId}', [InventoryController::class, 'getLocationsByWarehouse']);


use App\Http\Controllers\InboundRequestController;
use App\Http\Controllers\OutboundRequestController;

Route::resource('warehouses', WarehouseController::class);
Route::resource('locations', LocationController::class);

// Inbound Requests
Route::resource('inbound_requests', InboundRequestController::class);
Route::post('/inbound_requests/{id}/handle-discrepancy', [InboundRequestController::class, 'handleDiscrepancyAction'])->name('inbound_requests.handleDiscrepancyAction');
Route::get('/inbound_requests/{id}/complete', [InboundRequestController::class, 'complete'])->name('inbound_requests.complete');
Route::post('/inbound_requests/{id}/complete', [InboundRequestController::class, 'storeCompletion'])->name('inbound_requests.storeCompletion');


// Outbound Requests
Route::resource('outbound_requests', OutboundRequestController::class);
Route::get('/outbound_requests/{outboundRequest}/check-stock', [OutboundRequestController::class, 'checkStockAvailability'])->name('outbound_requests.checkStock');
Route::get('/outbound_requests/{outboundRequest}/reject', [OutboundRequestController::class, 'rejectRequest'])->name('outbound_requests.reject');


Route::resource('suppliers', SupplierController::class);


Route::resource('customers', CustomerController::class);


Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/', function () {
    return view('dashboard');
});
