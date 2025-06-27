<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Api\Auth\AuthController;

use App\Models\User;

Route::middleware([

])->group(function () {
    Route::prefix('auth')->controller(AuthController::class)->group(function () {
        Route::post('/login', 'login');
        Route::post('/register', 'register');
        Route::post('/forgot-password', 'forgotPassword');
        Route::post('/reset-password', 'resetPassword');
    });
});


Route::middleware([
    'auth:sanctum',
])->group(function () {
    Route::prefix('auth')->controller(AuthController::class)->group(function () {
        Route::post('/logout', 'logout');
        
        Route::post('/send-verification-email', 'sendVerificationEmail');
        Route::get('/verify-email/{id}/{hash}', 'verifyEmail')
            ->middleware('signed')
            ->name('verification.verify');
    });

    Route::get('/auth/user', function (Request $request) {
        return $request->user();
    });
});