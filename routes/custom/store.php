<?php 

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CompanyMiddleware;
use App\Http\Middleware\StoreMiddleware;

use App\Http\Controllers\Store\StoreEmployeeController;
use App\Http\Controllers\Store\StoreRoleController;
use App\Http\Controllers\Store\StorePermissionController;
use App\Http\Controllers\Store\StoreProductController;
use App\Http\Controllers\Store\StoreRestockController;

Route::middleware([
    'auth',
    CompanyMiddleware::class,
    StoreMiddleware::class,
])->group(function () {
    // Store
    Route::get('/dashboard-store', function () { return view('store.dashboard-store'); })->name('dashboard-store');

    Route::resource('store_employees', StoreEmployeeController::class);
    
    Route::resource('store_roles', StoreRoleController::class);
    Route::resource('store_permissions', StorePermissionController::class);

    Route::resource('store_restocks', StoreRestockController::class);
    Route::delete('store_restocks/{id}/cancel', [StoreRestockController::class, 'cancelRequest'])->name('store_restocks.cancel');

    Route::resource('store_customers', StorePermissionController::class);
    Route::resource('store_pos', StorePermissionController::class);

    Route::resource('store_products', StoreProductController::class);
    Route::resource('store_warehouses', StorePermissionController::class);
    Route::resource('store_inventories', StorePermissionController::class);
    Route::resource('store_inbounds', StorePermissionController::class);
    Route::resource('store_outbounds', StorePermissionController::class);
});