<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Company\Warehouse;

use App\Models\Company\Inventory\Inventory;
use App\Models\Company\Inventory\InventoryMovement;

class WarehouseInventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $warehouse = Warehouse::findOrFail(session('company_warehouse_id'));

        $inventories = Inventory::with(['product', 'warehouse_location'])
                                ->where('warehouse_id', $warehouse->id)
                                ->get();

        return view('warehouse.warehouse_inventories.index', compact('inventories'));
    }

    
    public function movement_index()
    {
        $warehouse = Warehouse::findOrFail(session('company_warehouse_id'));

        $inventory_movements = InventoryMovement::with(['product', 'warehouse', 'warehouse_location'])
                                ->where('warehouse_id', $warehouse->id)
                                ->orderBy('created_at', 'desc')
                                ->get();

        return view('warehouse.warehouse_inventories.movement_index', compact('inventory_movements'));
    }
}
