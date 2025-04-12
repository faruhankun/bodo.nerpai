<?php

namespace App\Http\Controllers\Store;

use App\Models\Store\StorePos;
use App\Models\Store\StorePosProduct;
use App\Models\Store\StoreCustomer;
use App\Models\Store\StoreEmployee;
use App\Models\Store\StoreProduct;
use App\Models\Store\StoreInventory;
use App\Models\Store\StoreLocation;
use App\Models\Store\StoreInventoryMovement;

use App\Models\Customer;
use App\Models\Company\Product;
use App\Models\Company\Courier;
use App\Models\Company\Finance\Account;

use App\Services\Company\Finance\JournalEntryService;

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

        $store_products = StoreInventory::with(['store_product', 'store_location'])
                                ->where('store_id', $store_id)
                                ->get();
        
		$store_employee = StoreEmployee::where('store_id', $store_id)
										->findOrFail(session('company_store_employee_id'));

		$payment_methods = Account::where('type_id', 1)->get();         // Kas & Bank

		return view('store.store_pos.create', compact('store_customers', 'store_products', 'store_employee', 'payment_methods'));
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
			'products.*.store_location_id' => 'nullable|integer',
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
		$discount_amount = 0;
		$product_to_inventory_movements = [];
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
			$discount_amount += ($product['discount'] / 100) * $product['quantity'] * $product['price'];

			$product_to_inventory_movements[] = [
				'store_id' => $store_id,
				'store_product_id' => $product['product_id'],
				'store_location_id' => $product['store_location_id'],
				'quantity' => $product['quantity'],
				'cost_per_unit' => $product['cost_per_unit'],
				'notes' => $product['notes'],
				'source_type' => 'POS',
				'source_id' => $pos->id,
			];
		}

		// listing to inventory movement
		foreach($product_to_inventory_movements as $product_to_inventory_movement){
			$inventory_movement = StoreInventoryMovement::create($product_to_inventory_movement);
	
			// input cogs
			$inventory_movement->postingCost();

			// update inventory
			$inventory_movement->postMovementtoStoreInventory();
		}

        $pos->total_amount = $total_amount;
		$data['discount_amount'] = $discount_amount;
		
		// input pendapatan
		$this->postingJournalStorePos($pos, $data);
		
        $pos->save();

		return redirect()->route('store_pos.show', $pos->id)->with('success', "POS {$pos->number} created successfully.");
	}


	public function postingJournalStorePos($pos, $data = []){
		$journalServices = app(JournalEntryService::class);
		if(!isset($data['discount_amount'])) $data['discount_amount'] = 0;

		$details = [
			[
				'account_id' => $pos->payment_method,							// Kas / Bank
				'debit' => $pos->payment_amount,
			],
			[
				'account_id' => get_company_setting('comp.account_revenue'),	// Pendapatan
				'credit' => $pos->total_amount + $data['discount_amount'],
			],
		];

		if($data['discount_amount'] > 0){
			$details[] = [
				'account_id' => get_company_setting('comp.account_discount_sales'),		// Diskon
				'debit' => $data['discount_amount'],
			];
		}

		$journalServices->addJournalEntry([
				'created_by' => $pos->store_employee->employee->id,
				'date' => $pos->date,
				'source_type' => 'POS',
				'source_id' => $pos->id,
				'type' => 'POS',
				'description' => "POS {$pos->number}",
				'total' => $pos->total_amount + $data['discount_amount'],
			],
			$details
		);
	}


	public function show($id)
	{
		$store_pos = StorePos::with(['store_customer', 'store_employee', 'store_pos_products'])->findOrFail($id);

		$payment_methods = Account::where('type_id', 1)->get();         // Kas & Bank

		return view('store.store_pos.show', compact('store_pos', 'payment_methods'));
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
		
		$pdf = Pdf::loadView('company.invoices.pos_receipt', compact('store_pos'));
		
		return $pdf->stream('pos.pdf');
	}


	public function invoice($id)
	{
		$store_pos = StorePos::with(['store_customer', 'store_employee', 'store_pos_products'])->findOrFail($id);

		return view('company.invoices.pos_invoice', compact('store_pos'));
	}
}
