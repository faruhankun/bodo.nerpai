<?php
namespace App\Http\Controllers\Store;

use Yajra\DataTables\Facades\DataTables;

use Illuminate\Support\Str;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\Store\StoreRole;
use App\Models\Store\StorePermission;

class StoreRoleController extends Controller
{
    public function index()
    {
        $store_roles = StoreRole::with('permissions')->get();
        return view('store.store_roles.index', compact('store_roles'));
    }
    
    public function create()
    {
        
    }

    public function store(Request $request)
    {
        
    }
    public function edit(string $id)
    {
        
    }

    public function update(Request $request, string $id)
    {
        
    }

    public function destroy(string $id)
    {
        
    }
}