<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::middleware([

])->group(function () {
    Route::prefix('ai')->group(function () {
        Route::post('/chat', function (Request $request) {
            return response()->json([
                'response' => 'Hai, saya Bodo, request anda: ' . json_encode($request->all()),
            ]);
        });
    });
});