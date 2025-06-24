<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CompanyMiddleware;

use App\Http\Controllers\Primary\Transaction\TradeController;
use App\Http\Controllers\Primary\Player\ContactController;


use App\Http\Controllers\Primary\Inventory\AccountController;


Route::middleware([
])->group(function () {
    
});



use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (!Auth::attempt($credentials)) {
        return response()->json(['message' => 'Login gagal'], 401);
    }
    
    $user = Auth::user();
    $token = $user->createToken('react-app')->plainTextToken;
    
    // return $credentials;

    return [
        'token' => $token,
        'user' => $user,
    ];
});

Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();

    return response()->json(['message' => 'Logout berhasil']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// require routes dari primary
foreach (glob(base_path('routes/api/*.php')) as $file) {
    require $file;
}