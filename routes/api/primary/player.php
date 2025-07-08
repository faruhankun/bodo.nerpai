<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Primary\PlayerController;
use App\Http\Controllers\Primary\Player\ContactController;

Route::middleware([
])->group(function () {
    // player
    Route::get('players/spaces', [PlayerController::class, 'getRelatedSpaces'])->name('players.spaces');
    Route::get('players/data', [PlayerController::class, 'getData'])->name('players.data');



    // Contacts
    Route::post('contacts/exim', [ContactController::class, 'eximData'])->name('contacts.exim');
    Route::get('contacts/exim', [ContactController::class, 'eximData'])->name('contacts.exim');

    Route::get('contacts/summary', [ContactController::class, 'summary'])->name('contacts.summary');
    Route::get('contacts/data', [ContactController::class, 'getContactsData'])->name('contacts.data');
    Route::resource('contacts', ContactController::class);
});