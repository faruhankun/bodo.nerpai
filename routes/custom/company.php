<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CompanyMiddleware;

use App\Http\Controllers\Company\CompanySettingController;

use App\Http\Controllers\Company\CompanyUserController;
use App\Http\Controllers\Company\CompanyRoleController;
use App\Http\Controllers\Company\CompanyPermissionController;

use App\Http\Controllers\Company\EmployeeController;


use App\Http\Controllers\Company\PurchaseController;
use App\Http\Controllers\Company\PurchaseInvoiceController;
use App\Http\Controllers\Company\SupplierController;


use App\Http\Controllers\Company\SaleController;
use App\Http\Controllers\Company\SaleInvoiceController;
use App\Http\Controllers\Company\CustomerController;


use App\Http\Controllers\Company\ProductController;
use App\Http\Controllers\Company\WarehouseLocationController;
use App\Http\Controllers\Company\InventoryTransferController;

use App\Http\Controllers\Warehouse\InboundController;
use App\Http\Controllers\Warehouse\OutboundController;
use App\Http\Controllers\Company\InventoryController;

use App\Http\Controllers\Company\ShipmentController;
use App\Http\Controllers\Company\CourierController;


use App\Http\Controllers\Company\Finance\AccountController;

use App\Http\Controllers\Company\Finance\JournalEntryController;
use App\Http\Controllers\Company\Finance\PaymentController;
use App\Http\Controllers\Company\Finance\PayableController;
use App\Http\Controllers\Company\Finance\ReceivableController;
use App\Http\Controllers\Company\Finance\ExpenseController;
use App\Http\Controllers\Company\ReportController;

// Company
Route::middleware([
    'auth',
    CompanyMiddleware::class,
])->group(function () {
    // CompanySettings
    Route::resource('company_settings', CompanySettingController::class);

    Route::resource('company_users', CompanyUserController::class);
    Route::delete('/company_users/cancelInvite/{id}', [CompanyUserController::class, 'cancelInvite'])->name('company_users.cancelInvite');

    Route::resource('company_roles', CompanyRoleController::class);
    Route::get('/company_roles', [CompanyRoleController::class, 'index'])->name('company_roles.index');
    Route::get('/company_roles/data', [CompanyRoleController::class, 'getRolesData'])->name('company_roles.data');

    Route::resource('company_permissions', CompanyPermissionController::class);

    Route::resource('employees', EmployeeController::class);


    route::resource("purchases", PurchaseController::class);
    Route::post('purchases/{purchases}/action/{action}', [PurchaseController::class, 'handleAction'])->name('purchases.action');
    Route::get('purchases/{id}/duplicate', [PurchaseController::class, 'duplicate'])->name('purchases.duplicate');
    Route::get('/suppliers/data', [SupplierController::class, 'getSuppliersData'])->name('suppliers.data');
    Route::resource('suppliers', SupplierController::class);
    route::get('/customers/data', [CustomerController::class, 'getCustomersData'])->name('customers.data');
    Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search');
    route::resource("customers", CustomerController::class);



    Route::resource('sales', SaleController::class);
    Route::post('sales/{id}/action/{action}', [SaleController::class, 'handleAction'])->name('sales.action');

    Route::get('/products/data', [ProductController::class, 'getProductsData'])->name('products.data');
    route::resource("products", ProductController::class);
    route::resource("warehouse_locations", WarehouseLocationController::class);

    route::resource("inventory_transfers", InventoryTransferController::class);
    Route::post('inventory_transfers/{id}/action/{action}', [InventoryTransferController::class, 'handleAction'])->name('inventory_transfers.action');
    Route::post('inventory_transfers/storeRequest', [InventoryTransferController::class, 'storeRequest'])->name('inventory_transfers.storeRequest');
    Route::resource('inventory', InventoryController::class)->except(['show']);
    Route::get('/inventory/adjust', [InventoryController::class, 'adjust'])->name('inventory.adjust');
    Route::get('/inventory/history', [InventoryController::class, 'history'])->name('inventory.history');


    Route::resource("shipments", ShipmentController::class);
    Route::post('shipments/{shipments}/action/{action}', [ShipmentController::class, 'handleAction'])->name('shipments.action');
    Route::get('shipments/{id}/confirm', [ShipmentController::class, 'confirm'])->name('shipments.confirm');
    Route::put('shipments/{id}/confirm-update', [ShipmentController::class, 'confirm_update'])->name('shipments.confirm-update');
    Route::get('shipments/{id}/confirm-show', [ShipmentController::class, 'confirm_show'])->name('shipments.confirm-show');
    Route::resource("couriers", CourierController::class);



    // finances
    Route::resource('purchase_invoices', PurchaseInvoiceController::class);
    Route::post('purchase_invoices/{id}/action/{action}', [PurchaseInvoiceController::class, 'handleAction'])->name('purchase_invoices.action');
    Route::resource('sale_invoices', SaleInvoiceController::class);
    Route::post('sale_invoices/{id}/action/{action}', [SaleInvoiceController::class, 'handleAction'])->name('sale_invoices.action');
    Route::resource('payments', PaymentController::class);
    Route::post('payments/{id}/action/{action}', [PaymentController::class, 'handleAction'])->name('payments.action');
    Route::resource('payables', PayableController::class);
    Route::post('payables/{id}/action/{action}', [PayableController::class, 'handleAction'])->name('payables.action');
    Route::resource('receivables', ReceivableController::class);
    Route::post('receivables/{id}/action/{action}', [ReceivableController::class, 'handleAction'])->name('receivables.action');
    Route::resource('accounts', AccountController::class);
    Route::resource("journal_entries", JournalEntryController::class);
    Route::resource('expenses', ExpenseController::class);
    Route::post('expenses/{id}/action/{action}', [ExpenseController::class, 'handleAction'])->name('expenses.action');

    Route::resource("reports", ReportController::class);
});
