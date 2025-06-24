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
    
});


// require routes dari primary
foreach (glob(base_path('routes/api/primary/*.php')) as $file) {
    require $file;
}
