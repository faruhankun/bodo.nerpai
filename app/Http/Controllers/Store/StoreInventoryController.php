<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Store\StoreInventory;
use App\Models\Company\Store;

class StoreInventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $store = Store::findOrFail(session('company_store_id'));

        $inventories = StoreInventory::with(['store_product', 'warehouse_location'])
                                ->where('store_id', $store->id)
                                ->get();

        return view('store.store_inventories.index', compact('inventories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
