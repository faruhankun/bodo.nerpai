<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Primary\PlayerController;

Route::middleware([
])->group(function () {
    // Account
    Route::get('players/spaces', [PlayerController::class, 'getRelatedSpaces'])->name('players.spaces');
});