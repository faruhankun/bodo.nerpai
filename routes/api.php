<?php

use App\Http\Controllers\SupplierController;

use App\Http\Controllers\Company\Customer\CustomerController;

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CompanyMiddleware;

// Route::prefix("supplier")->group(function () {
//     Route::get("/", [SupplierController::class, "index"]);
//     Route::get("/{supplier}", [SupplierController::class, "show"]);
//     Route::post("/", [SupplierController::class, "store"]);
//     Route::patch("/{supplier}", [SupplierController::class, "update"]);
//     Route::delete("/{supplier}", [SupplierController::class, "destroy"]);
// });
Route::middleware([
])->group(function () {
    // Route::resource('customers', CustomerController::class);
});
