<?php

namespace App\Http\Controllers\Store;

use App\Models\Store\StorePos;
use App\Models\Store\StorePosProduct;
use App\Models\Store\StoreCustomer;
use App\Models\Store\StoreEmployee;
use App\Models\Store\StoreProduct;
use App\Models\Store\StoreInventory;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Company\Courier;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Http\Controllers\Controller;

class StorePosController extends Controller
{
	public function index(Request $request)
	{
        $store = session('company_store_id');

		$store_pos = StorePos::with(['store_customer', 'store_employee'])
                            ->where('store_id', $store)
                            ->orderBy('updated_at', 'desc')
                            ->get();

		return view('store.store_pos.index', compact('store_pos'));
	}


	public function create()
	{
        $store_id = session('company_store_id');

		$store_customers = StoreCustomer::all();

        $store_products = StoreInventory::with(['store_product', 'warehouse_location'])
                                ->where('store_id', $store_id)
                                ->get();
        
		$store_employee = StoreEmployee::where('store_id', $store_id)
										->findOrFail(session('company_store_employee_id'));

		return view('store.store_pos.create', compact('store_customers', 'store_products', 'store_employee'));
	}

	public function store(Request $request)
	{
        $validated = $request->validate([
			'store_customer_id' => 'nullable|integer',
			'date' => 'required|date',
            'payment_method' => 'required|string',
            'payment_amount' => 'required|numeric|min:0',
			'products' => 'required|array', // Expecting products as an array
			'products.*.product_id' => 'required|integer',
			'products.*.price' => 'required|numeric|min:0',
			'products.*.quantity' => 'required|integer|min:1',
            'products.*.discount' => 'nullable|numeric|min:0',
            'products.*.subtotal' => 'required|numeric|min:0',
			'products.*.notes' => 'nullable|string', // Note for each product
            'products.*.cost_per_unit' => 'required|numeric|min:0',
            'products.*.total_cost' => 'required|numeric|min:0',
			'products.*.warehouse_location_id' => 'required|integer',
            'notes' => 'nullable|string',
		]);

        $store_id = session('company_store_id');
		$store_employee_id = session('company_store_employee_id');

		// Create pos
		$pos = StorePos::create([
			'date' => $validated['date'],
			'store_customer_id' => $validated['store_customer_id'],
			'store_id' => $store_id,
			'store_employee_id' => $store_employee_id,
            'payment_method' => $validated['payment_method'],
            'payment_amount' => $validated['payment_amount'],
            'status' => 'PAID',
            'notes' => $validated['notes'],
		]);
		$pos->generatePosNumber();
        
		// Create Sales Products
        $total_amount = 0;
		foreach ($validated['products'] as $product) {
            StorePosProduct::create([
                'store_pos_id' => $pos->id,
                'store_product_id' => $product['product_id'],
                'quantity' => $product['quantity'],
                'price' => $product['price'],
                'discount' => $product['discount'],
                'notes' => $product['notes'],
                'subtotal' => $product['subtotal'],
                'cost_per_unit' => $product['cost_per_unit'],
                'total_cost' => $product['total_cost'],
            ]);

            $total_amount += $product['subtotal'];

			$product_inventory = StoreInventory::where('store_id', $store_id)
							->where('store_product_id', $product['product_id'])
							->where('warehouse_location_id', $product['warehouse_location_id'])
							->first();
			$product_inventory->decrement('quantity', $product['quantity']);
			$product_inventory->save();
		}
        
        $pos->total_amount = $total_amount;
        $pos->save();

		// Update Inventory

		return redirect()->route('store_pos.show', $pos->id)->with('success', "POS {$pos->number} created successfully.");
	}


	public function show($id)
	{
		$store_pos = StorePos::with(['store_customer', 'store_employee', 'store_pos_products'])->findOrFail($id);
		return view('store.store_pos.show', compact('store_pos'));
	}


	public function edit($id)
	{
		
	}


	public function update(Request $request, $id)
	{

	}


	public function destroy($id)
	{
		$store_pos = StorePos::findOrFail($id);
		$store_pos->delete();

		return redirect()->route('store_pos.index')->with('success', "POS {$store_pos->number} deleted successfully.");
	}


	public function printPos($id)
	{
		$store_pos = StorePos::with(['store_customer', 'store_employee', 'store_pos_products'])->findOrFail($id);
		
		$pdf = Pdf::loadView('invoices.pos_receipt', compact('store_pos'));
		
		return $pdf->stream('pos.pdf');
	}
}
