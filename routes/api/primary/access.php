<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Primary\Access\VariableController;
use App\Http\Controllers\Primary\Access\RoleController;
use App\Http\Controllers\Primary\Access\PermissionController;



Route::middleware([
])->group(function () {
    Route::get('variables/get', [VariableController::class, 'getVariable'])->name('variables.get');
    Route::get('variables/data', [VariableController::class, 'getVariablesData'])->name('variables.data');



    // roles
    Route::prefix('roles')->controller(RoleController::class)->group(function () {
        Route::get('/data', 'getData');
        Route::post('/manage', 'manageRoles');
    });
    Route::resource('roles', RoleController::class);



    // Permissions
    Route::prefix('permissions')->controller(PermissionController::class)->group(function () {
        Route::get('/data', 'getData');
    });
    Route::resource('permissions', PermissionController::class);
});