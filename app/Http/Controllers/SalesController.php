<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SalesProduct;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Expedition;
use App\Models\OutboundRequest;
use App\Models\Customer;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function index(Request $request)
	{
		$query = Sale::with(['products.salesProducts', 'warehouse', 'customer'])->orderBy('sale_date', 'desc');

		// Optional: Filter by status
		if ($request->has('status')) {
			$query->where('status', $request->input('status'));
		}

		$sales = $query->get();
		return view('sales.index', compact('sales'));
	}

	
	public function create()
	{
		$warehouses = Warehouse::all();
		$products = Product::all();
		$expeditions = Expedition::all(); // Fetch expeditions
		$customers = Customer::all();
		return view('sales.create', compact('warehouses', 'products', 'expeditions', 'customers'));
	}

	public function store(Request $request)
	{
		$validated = $request->validate([
			'customer_id' => 'required|integer',
			'sale_date' => 'required|date',
			'warehouse_id' => 'required|exists:warehouses,id',
			'products' => 'required|array', // Expecting products as an array
			'products.*.product_id' => 'required|exists:products,id',
			'products.*.quantity' => 'required|integer|min:1',
			'products.*.price' => 'required|numeric|min:0',
			'products.*.note' => 'nullable|string', // Note for each product
		]);

		// Create sale
		$sale = Sale::create([
			'customer_id' => $validated['customer_id'],
			'sale_date' => $validated['sale_date'],
			'warehouse_id' => $validated['warehouse_id'],
			'total_amount' => collect($validated['products'])->sum(function ($product) {
				return $product['quantity'] * $product['price'];
			}),
		]);

		// Create Sales Products
		foreach ($validated['products'] as $product) {
			SalesProduct::create([
				'sale_id' => $sale->id,
				'product_id' => $product['product_id'],
				'quantity' => $product['quantity'],
				'price' => $product['price'],
				'note' => $product['note'] ?? null,
			]);
		}

		$sale->save();
	
		return redirect()->route('sales.index')->with('success', 'Sale created successfully.');
	}


	public function show($id)
	{
		$sale = Sale::with(['products', 'warehouse', 'expedition', 'customer'])->findOrFail($id);
		return view('sales.show', compact('sale'));
	}


	public function edit($id)
	{
		$sale = Sale::with('products')->findOrFail($id);
		$customers = Customer::all();
		$warehouses = Warehouse::all();
		$products = Product::all();
		$expeditions = Expedition::all(); // Fetch expeditions
		$outboundRequests = OutboundRequest::all(); // Fetch OutboundRequest
		return view('sales.edit', compact('sale', 'warehouses', 'products', 'expeditions', 'outboundRequests', 'customers'));
	}


	public function update(Request $request, $id)
	{
		$validated = $request->validate([
			'customer_id' => 'required|integer',
			'sale_date' => 'required|date',
			'warehouse_id' => 'required|exists:warehouses,id',
			'products' => 'required|array', // Validate products as an array
			'products.*.product_id' => 'required|exists:products,id',
			'products.*.quantity' => 'required|integer|min:1',
			'products.*.price' => 'required|numeric|min:0',
			'products.*.note' => 'nullable|string', // Handle product notes
			'expedition_id' => 'required|exists:expeditions,id', // Validate expedition
			'estimated_shipping_fee' => 'nullable|numeric|min:0',
		]);

		$sale = Sale::findOrFail($id);
		$sale->update([
			'customer_id' => $validated['customer_id'],
			'sale_date' => $validated['sale_date'],
			'warehouse_id' => $validated['warehouse_id'],
			'expedition_id' => $validated['expedition_id'], // Update expedition in the same call
			'estimated_shipping_fee' => $validated['estimated_shipping_fee'],
		]);

		// Sync products with updated quantities, prices, and notes
		$productData = [];
		foreach ($validated['products'] as $product) {
			$productData[$product['product_id']] = [
				'quantity' => $product['quantity'],
				'price' => $product['price'],
				'note' => $product['note'] ?? null, // Handle notes
			];
		}
		$sale->products()->sync($productData);

		// Update total amount
		$totalAmount = collect($productData)->sum(function ($details) {
			return $details['quantity'] * $details['price'];
		});
		$sale->update(['total_amount' => $totalAmount]);

		return redirect()->route('sales.index')->with('success', 'Sale updated successfully.');
	}

	
	public function destroy($id)
	{
		$sale = Sale::findOrFail($id);
		$sale->delete();

		return redirect()->route('sales.index')->with('success', 'Sale deleted successfully.');
	}

	public function updateStatus(Sale $sale, $status)
	{
		// Set status to "Unpaid" and request outbound
		if ($sale->status == 'Planned' && $status == 'Unpaid') {
			$sale->status = $status;
			$sale->save();

			// Create an inbound request when moving to "In Transit"
			$requestedQuantities = [];
			foreach ($sale->products as $product) {
				$requestedQuantities[$product->id] = $product->pivot->quantity;
			}

			OutboundRequest::create([
				'sales_order_id' => $sale->id,
				'warehouse_id' => $sale->warehouse_id,
				'requested_quantities' => $requestedQuantities,
				'received_quantities' => [],
				'status' => 'Requested',
				'notes' => 'Outbound request created upon status change to Unpaid',
			]);
		}
		
		// Set status to "Pending Shipment" after confirming payment
		if ($status == 'Pending Shipment') {
			// Check if the sale is in the correct status
			if ($sale->status !== 'Unpaid') {
				return redirect()->back()->with('error', 'This sale cannot be marked as paid.');
			}
			
			// Update the related Outbound Request status
			$outboundRequest = OutboundRequest::where('sales_order_id', $sale->id)
												->where('status', 'Pending Confirmation')->last();
			if ($outboundRequest) {
				$outboundRequest->status = 'Packing & Shipping';
				$outboundRequest->save();
			}
			
			$sale->update(['status' => $status]);
		    return redirect()->route('sales.show', $sale->id)
							->with('success', 'Sale marked as paid and is now pending shipment.');
		}
		
		$sale->update(['status' => $status]);

		return redirect()->route('sales.edit', $sale->id)
						 ->with('success', 'Status updated successfully!');
	}

}
