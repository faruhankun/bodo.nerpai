<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;

use App\Models\Company\Sale\Sale;
use App\Models\Company\Sale\SaleItems;
use App\Models\Company\Sale\SaleInvoice;

use App\Models\Company\Product;
use App\Models\Company\Warehouse;
use App\Models\Company\Courier;
use App\Models\Company\Customer;
use App\Models\Employee;
use App\Models\CustomerComplaint;
use Illuminate\Http\Request;

use App\Models\Warehouse\Outbound;
use App\Models\Warehouse\OutboundItem;

use App\Models\Company\Inventory\Inventory;
use App\Models\Company\Inventory\InventoryMovement;

use App\Models\Space\Company;

// services
use App\Services\Company\Finance\JournalEntryService;

class SaleController extends Controller
{
	public function index(Request $request)
	{
		$sales = Sale::with(['warehouse', 'consignee'])->orderBy('updated_at', 'desc')->get();
		return view('company.sales.index', compact('sales'));
	}


	public function create()
	{
		$warehouses = Warehouse::all();

		return view('company.sales.create', compact('warehouses'));
	}

	public function store(Request $request)
	{
		$validated = $request->validate([
			'customer_id' => 'required|exists:customers,id',
			'date' => 'required|date',
			'warehouse_id' => 'required|exists:warehouses,id',
		]);
		
		$employee = session('employee');

		// Create sale
		$sale = Sale::create([
			'date' => $validated['date'],
			'customer_id' => $validated['customer_id'],
			'consignee_type' => 'CUST',
			'consignee_id' => $validated['customer_id'],
			'warehouse_id' => $validated['warehouse_id'],
			'employee_id' => $employee->id,
			'status' => 'SO_OFFER',
		]);
		$sale->generateSoNumber();
		$sale->save();

		return redirect()->route('sales.edit', $sale->id)->with('success', "Sale {$sale->so_number} created successfully.");
	}


	public function show($id)
	{
		$sale = Sale::with(['items', 'invoices', 'courier', 'outbounds', 'shipments'])->findOrFail($id);

		return view('company.sales.show', compact('sale'));
	}


	public function edit($id)
	{
		$sale = Sale::with('items', 'customer')->findOrFail($id);
		$customers = Customer::all();
		$warehouses = Warehouse::all();

		$items = Product::all();
		$couriers = Courier::all(); // Fetch couriers
		$items = Inventory::all();

		return view('company.sales.edit', compact('sale', 'warehouses', 'couriers', 'customers', 'items'));
	}


	public function update(Request $request, $id)
	{
		$validated = $request->validate([
			'date' => 'required|date',
			'customer_id' => 'required|exists:customers,id',
			'warehouse_id' => 'required|exists:warehouses,id',
			'items' => 'nullable|array', // Validate items as an array
			'items.*.item_type' => 'required|string',
			'items.*.item_id' => 'required|integer',
			'items.*.quantity' => 'required|integer|min:1',
			'items.*.discount' => 'nullable|numeric|min:0',
			'items.*.price' => 'required|numeric|min:0',
			'items.*.notes' => 'nullable|string', // Handle product notes
			'courier_id' => 'nullable|exists:couriers,id', // Validate courier
			'shipping_fee_discount' => 'nullable|numeric|min:0',
			'estimated_shipping_fee' => 'nullable|numeric|min:0',
		]);

		$employee = session('employee');
		
		$sale = Sale::with('items')->findOrFail($id);
		$sale->update($validated);

		$sale->consignee_type = 'CUST';
		$sale->consignee_id = $validated['customer_id'];

		$sale->generateSoNumber();

		// Sync items with updated quantities, prices, and notes
		$this->SyncSaleItems($request, $sale);

		// Update total amount
		$sale->save();

		return redirect()->route('sales.show', $sale->id)->with('success', "Sale {$sale->so_number} updated successfully.");
	}

	public function SyncSaleItems($request, $sale){
        $sale_items = [];
		$total_amount = 0;


        // build transfer items
        if(isset($request->items)){
            foreach ($request->items as $item) {
				$inventory = Inventory::findOrFail($item['item_id']);

                $sale_items[] = [
                    'sale_id' => $sale->id,
                    'item_type' => $item['item_type'],
                    'item_id' => $inventory->product->id,
					'inventory_id' => $inventory->id,
                    'quantity' => $item['quantity'],
					'discount' => $item['discount'],
					'price' => $item['price'],
					'cost_per_unit' => $inventory->cost_per_unit,
					'total_cost' => $item['quantity'] * $inventory->cost_per_unit,
                    'notes' => $item['notes'],
                ];

				$total_amount += $item['quantity'] * $item['price'] * (1 - ($item['discount'] / 100));
            }
        }



        // Sync 
        if(is_null($request->items)) {
            $request->items = [];
        }
        $request_item_ids = array_column($request->items, 'item_id');

        // delete items that are not in the request
        $sale->items()->whereNotIn('item_id', $request_item_ids)->delete();

		foreach($sale_items as $item){
			$sale->items()->updateOrCreate([
					'sale_id' => $sale->id,
					'item_type' => $item['item_type'],
					'item_id' => $item['item_id'],
				], $item
			);
		}
        // $sale->items()->upsert($sale_items,
        //     ['sale_id', 'item_type', 'item_id'],
        //     ['inventory_id', 'quantity', 'discount', 'price', 'notes']
        // );

		$sale->total_amount = $total_amount;
        $sale->save();
    }



	public function destroy($id)
	{
		$sale = Sale::findOrFail($id);
		$sale->delete();

		return redirect()->route('sales.index')->with('success', 'Sale deleted successfully.');
	}


	public function handleAction(Request $request, $sales, $action){
		$sale = Sale::findOrFail($sales);

		switch($action){
			case 'SO_REQUEST':
				$this->sendSORequestToCustomer($sale);
				break;
			case 'SO_CONFIRMED':
				return $this->inputInvoiceForCustomer($sale);
				break;
			case 'SO_DP_CONFIRMED':
				$this->confirmPaymenttoCustomer($sale);
				break;
			case 'SO_OUTBOUND_REQUEST':
				return $this->requestOutboundforSale($request, $sale);
				break;
			case 'SO_PAYMENT_COMPLETION':
				$this->paymentCompletion($sale);
				break;
			case 'SO_COMPLETED':
				return $this->completeSale($sale);
				break;
			default:
				abort(404);
		}

		return redirect()->route('sales.show', $sale->id)->with('success', "Sale {$sale->number} updated successfully.");
	}

	public function sendSORequestToCustomer($sale){
		$sale->status = 'SO_REQUEST';

		// Create Request to Customer if connected

		$sale->save();
	}

	public function inputInvoiceForCustomer($sale){
		$sale_invoice = SaleInvoice::create([
			'sale_id' => $sale->id,
			'date' => date('Y-m-d'),
			'cost_products' => $sale->total_amount,
			'cost_packing' => 0,
			'cost_freight' => $sale->estimated_shipping_fee - $sale->shipping_fee_discount,
			'total_amount' => $sale->total_amount,
			'status' => 'unconfirmed',
		]);
		$sale_invoice->generateNumber();
		$sale_invoice->save();
		
		$sale->status = 'SO_CONFIRMED';
		$sale->save();

		return redirect()->route('sale_invoices.edit', $sale_invoice->id)->with('success', "Invoice {$sale_invoice->number} created successfully");
	}

	public function confirmPaymenttoCustomer($sale){
		$sale->status = 'SO_DP_CONFIRMED';

		// notify supplier

		$sale->save();
	}
	
	public function requestOutboundForSale(Request $request, $sale) {
        // check shipper
        if($sale->warehouse_id) {
            $this->requestOutboundWarehouse($request, $sale);
        // } else if($sale->shipper_type == 'ST') {
        //     $this->requestOutboundStore($request, $sale);
        } else {
            abort(404);
        }

        $sale->status = 'SO_OUTBOUND_REQUEST';
        $sale->save();
        
        return redirect()->route('sales.show', $sale->id)->with('success', "Outbound {$sale->number} request successfully.");
    }

    public function requestOutboundWarehouse(Request $request, $sale) {
        $employee = session('employee');
        
        $outbound = Outbound::create([
            'warehouse_id' => $sale->warehouse_id,
            'source_type' => 'SO',
            'source_id' => $sale->id,
            'employee_id' => $employee->id,
            'date' => now()->format('Y-m-d'),
            'status' => 'OUTB_REQUEST',
        ]);
        $outbound->generateNumber();
        $outbound->save();
        
        if(isset($sale->items)) {
			$outbound_items = [];
            foreach ($sale->items as $item) {
				$outbound_items[] = [
					'outbound_id' => $outbound->id,
					'inventory_id' => $item->inventory_id,
					'quantity' => $item->quantity,
					'cost_per_unit' => $item->cost_per_unit,
					'total_cost' => $item->total_cost,
					'notes' => $item->notes,
				];
			}
			$outbound->items()->createMany($outbound_items);
        }
    }

    public function requestOutboundStore(Request $request, $sale) {
    
    }

	public function paymentCompletion($sale){
		// notify customer

		$sale->status = 'SO_PAYMENT_COMPLETION';

		$sale->save();
	}

	public function completeSale($sale)
	{	
		// update stock in warehouse, etc
		$data = [];
		$data = $this->inputCogsFromWarehouse($sale);
		$this->inputRevenueSale($sale, $data);

		$sale->status = 'SO_COMPLETED';
		$sale->save();

		return redirect()->route('sales.show', $sale->id)->with('success', "Sale {$sale->number} completed successfully.");
	}

	
	public function inputCogsFromWarehouse($sale) 
	{
		$sale_items = $sale->items;

		$data['inventory_value'] = 0;
		$data['discount_amount'] = 0;
		foreach($sale_items as $item) {
			if($item->item_type == 'PRD') {
				$inventory_movement = InventoryMovement::create([
					'product_id' => $item->item_id,
					'warehouse_id' => $sale->warehouse_id,
					'warehouse_location_id' => $item->warehouse_location_id,
					'quantity' => $item->quantity,
					'cost_per_unit' => $item->cost_per_unit,
					'employee_id' => $sale->employee_id,
					'source_type' => 'SO',
					'source_id' => $sale->id,
				]);
	
				// posting cogs
				$inventory_movement->postCogs();
	
				// update inventory
				$inventory_movement->postMovement();
			}

			$data['inventory_value'] += $item->quantity * $item->cost_per_unit;
			$data['discount_amount'] += $item->quantity * $item->price * ($item->discount / 100);
		}
		
		return $data;
	}

	public function inputRevenueSale($sale, $data = []) 
	{
		// create journal revenue
		$journalService = app(JournalEntryService::class);
		if(!isset($data['discount_amount'])) $data['discount_amount'] = 0;

		$details = [
			[
				'account_id' => get_company_setting('comp.account_unearned_revenue'),	// Kas / Bank
				'debit' => $sale->total_amount,
			],
			[
				'account_id' => get_company_setting('comp.account_revenue'),	// Pendapatan
				'credit' => $sale->total_amount + $data['discount_amount'],
			],
		];

		if($data['discount_amount'] > 0){
			$details[] = [
				'account_id' => get_company_setting('comp.account_discount_sales'),		// Diskon
				'debit' => $data['discount_amount'],
			];
		}

		$journalService->addJournalEntry([
				'created_by' => $sale->employee_id,
				'source_type' => 'SO',
				'source_id' => $sale->id,
				'date' => date('Y-m-d'),
				'type' => 'SO',
				'description' => 'SO Revenue',
				'total' => $sale->total_amount + $data['discount_amount'],
			], $details
		);
	}
}
