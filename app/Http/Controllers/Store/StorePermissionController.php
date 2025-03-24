<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Store\StoreRole;
use App\Models\Store\StorePermission;

class StorePermissionController extends Controller
{
    public function index()
    {
        $store_permissions = StorePermission::all();
        return view('store.store_permissions.index', compact('store_permissions'));
    }
}
