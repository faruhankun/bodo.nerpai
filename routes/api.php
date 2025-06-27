<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CompanyMiddleware;


Route::middleware([
])->group(function () {
    
});


// require routes dari primary
foreach (glob(base_path('routes/api/*.php')) as $file) {
    require $file;
}