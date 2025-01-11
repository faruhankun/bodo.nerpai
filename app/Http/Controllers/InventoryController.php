<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Inventory;
use App\Models\InventoryHistory;
use App\Models\Location;
use App\Models\InboundRequest;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class InventoryController extends Controller
{
	public function index(Request $request)
	{
		$inventories = Inventory::with(['product', 'warehouse'])->get();
		
		// Fetch inventory details grouped by location
		$inventoryByLocations = InventoryHistory::with(['product', 'warehouse', 'location'])
			->select('product_id', 'warehouse_id', 'location_id', DB::raw('SUM(quantity) as total_quantity'))
			->groupBy('product_id', 'warehouse_id', 'location_id')
			->get();

		return view('inventory.index', compact('inventories', 'inventoryByLocations'));
	}



	public function history()
	{
		$history = InventoryHistory::with('product', 'warehouse')->orderBy('created_at', 'desc')->get();
		return view('inventory.history', compact('history'));
	}

	public function adjust()
	{
		$products = Product::all();
		$warehouses = Warehouse::all();
		$locations = Location::all(); // To populate room and rack options

		return view('inventory.adjust', compact('products', 'warehouses', 'locations'));
	}

	public function adjustInventory(Request $request)
	{
		$request->validate([
			'product_id' => 'required|exists:products,id',
			'warehouse_id' => 'required|exists:warehouses,id',
			'quantity' => 'required|integer',
			'transaction_type' => 'required|in:Addition,Reduction',
			'room' => 'required|string', // Validate room
			'rack' => 'required|string', // Validate rack
			'notes' => 'nullable|string'
		]);
		
		$productId = $request->input('product_id');
		$warehouseId = $request->input('warehouse_id');
		$quantity = $request->input('quantity');
		$transactionType = $request->input('transaction_type');
		
		$location = Location::where('warehouse_id', $warehouseId)
								->where('room', $request->room)
								->where('rack', $request->rack)
								->first();

		// Fetch or create the inventory entry for the product in the warehouse
		$inventory = Inventory::firstOrCreate(
			[
				'product_id' => $productId,
				'warehouse_id' => $warehouseId,
				'location_id' => $location->id,
			],
			['quantity' => 0]
		);

		// Adjust inventory quantity based on transaction type
		if ($transactionType === 'Addition') {
			$inventory->quantity += $quantity;
		} elseif ($transactionType === 'Reduction') {
			$inventory->quantity -= $quantity;
			if ($inventory->quantity < 0) {
				$inventory->quantity = 0; // Ensure quantity doesn't go negative
			}
		}

		$inventory->save();
		
		// Record transaction in inventory history
		InventoryHistory::create([
			'product_id' => $productId,
			'warehouse_id' => $warehouseId,
			'quantity' => $quantity,
			'transaction_type' => $transactionType,
			'location_id' => $location->id,
			'notes' => $request->input('notes'),
		]);

		return redirect()->route('inventory.index')->with('success', 'Inventory adjusted successfully.');
	}
	
	public function getLocationsByWarehouse($warehouseId)
	{
		// Fetch all locations in the specified warehouse, grouped by room and rack
		$locations = Location::where('warehouse_id', $warehouseId)
							 ->select('id', 'room', 'rack')
							 ->get();

		return response()->json($locations);
	}
	
	private function getStockDetails()
	{
		$inventories = Inventory::with(['product', 'warehouse', 'location'])->get();
		$inboundRequests = InboundRequest::where('status', 'In Transit')->get();
		//$salesOrders = Sale::where('status', 'Pending')->get();

		$availableStock = [];
		$incomingStock = [];
		$outgoingStock = [];

		foreach ($inventories as $inventory) {
			$availableStock[$inventory->product_id] = ($availableStock[$inventory->product_id] ?? 0) + $inventory->quantity;
		}

		foreach ($inboundRequests as $request) {
			foreach ($request->productQuantities() as $productId => $quantity) {
				$incomingStock[$productId] = ($incomingStock[$productId] ?? 0) + $quantity;
			}
		}
		
		/*
		foreach ($salesOrders as $order) {
			foreach ($order->productQuantities() as $productId => $quantity) {
				$outgoingStock[$productId] = ($outgoingStock[$productId] ?? 0) + $quantity;
			}
		}
		*/

		return compact('availableStock', 'incomingStock', 'outgoingStock');
	}


	public function reserveStock($sale)
	{
		foreach ($sale->products as $product) {
			// Logic to reserve stock here (e.g., adjust inventory reservation)
		}
	}
	
	
	public function getProductStock($warehouseId, $productId)
	{
		// Fetch the stock for the specified product in the specified warehouse
		$stock = Inventory::where('warehouse_id', $warehouseId)
						  ->where('product_id', $productId)
						  ->sum('quantity');

		return response()->json(['stock' => $stock]);
	}

	
	public function getLocations($warehouseId, $productId)
	{
		$locations = Inventory::where('warehouse_id', $warehouseId)
			->where('product_id', $productId)
			->with('location') // Ensure relationship with `Location` model exists
			->get(['location_id', 'quantity']);

		return response()->json($locations);
	}


}
