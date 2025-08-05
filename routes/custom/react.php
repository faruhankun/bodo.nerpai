<?php

use Illuminate\Support\Facades\Route;

use App\Http\Middleware\AppMiddleware;
use App\Http\Middleware\Space\SpaceMiddleware;




// Primary
Route::middleware([
    'auth',
    'verified',
    AppMiddleware::class,
    SpaceMiddleware::class,
])->group(function () {
    // accounts
    Route::get('/accounts', function () { return view('primary.inventory.accounts.page'); })->name('accounts');

    Route::get('/spacesr', function () { return view('primary.spaces.page'); })->name('spacesr');


    // Supplies
});
