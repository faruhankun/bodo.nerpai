<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Company\Inbound;
use App\Models\Company\InboundProducts;
use App\Models\Company\Purchase;
use App\Models\User;
use App\Models\Location;
use App\Models\Company\Inventory\Inventory;
use App\Models\Company\Inventory\InventoryMovement;
use App\Models\Company\Shipment;
use App\Models\Company\Warehouse;
use App\Models\Company\ShipmentConfirmation;

use App\Services\Company\Finance\JournalEntryService;

use Illuminate\Http\Request;

class InboundController extends Controller
{
    // Show all inbound requests
    public function index()
    {
		$warehouse = Warehouse::findOrFail(session('company_warehouse_id'));
        $inbounds = Inbound::with('shipment_confirmation', 'warehouse')
								->where('warehouse_id', $warehouse->id)
								->orderBy('created_at', 'desc')->get();
		$shipments_incoming = Shipment::where('consignee_type', 'WH')
										->where('consignee_id', $warehouse->id)
										->orderBy('created_at', 'desc')->get();
        return view('warehouse.warehouse_inbounds.index', compact('inbounds', 'shipments_incoming'));
    }

    // Create inbound request for a purchase
    public function create()
    {
		
    }


    // Store inbound request
    public function store(Request $request)
    {
        
    }
	

	public function show($id)
	{
		$inbound = Inbound::with('inbound_products', 'shipment_confirmation')->findOrFail($id);

		return view('warehouse.warehouse_inbounds.show', compact('inbound'));
	}


	// Edit method to show the edit view
    public function edit($id)
    {
        $inboundRequest = Inbound::with('purchase.products', 'warehouse')->findOrFail($id);
        $users = User::all(); // Assuming you want to select from all users

        return view('warehouse.warehouse_inbounds.edit', compact('inboundRequest', 'users'));
    }



	public function handleAction(Request $request, $inbounds, $action) {
		if($inbounds != '0') 
			$inbound = Inbound::findOrFail($inbounds);

		switch ($action) {
			case 'INB_REQUEST':
				// get shipment confirmation
				return $this->createInboundFromShipment($request->shipment_confirmation);
			case 'INB_PROCESS':
				return $this->inputInboundtoWarehouse($inbound);
				break;
			case 'INB_COMPLETED':
				return $this->completeInbound($inbound);
				break;
			default:
				abort(404);
		}

		return redirect()->route('warehouse_inbounds.index')->with('success', "Inbound request {$inbound->id} updated successfully.");
	}

	public function createInboundFromShipment($shipment_confirmation) {
		$shipment_confirmation = ShipmentConfirmation::with('products', 'shipment')->findOrFail($shipment_confirmation);
		$products = $shipment_confirmation->products;
		$shipment = $shipment_confirmation->shipment;
		$source_products = [];
		$cost_products = [];

		if($shipment->transaction_type == 'PO') {
			$source = $shipment->transaction;
			$source_products = $source->products;

			$cost_products = $source_products->pluck('pivot.buying_price', 'pivot.product_id');
		}

		$inbound = Inbound::create([
			'warehouse_id' => $shipment->consignee_id,
			'shipment_confirmation_id' => $shipment_confirmation->id,
			'employee_id' => $shipment_confirmation->employee_id,
			'status' => 'INB_REQUEST',
			'inbound_date' => date('Y-m-d'),
		]);

		foreach($products as $product){
			$cost_per_unit = $cost_products[$product->id] ?? 0.5 * $product->price;

			$inbound_product = InboundProducts::create([
				'inbound_id' => $inbound->id,
				'product_id' => $product->id,
				'warehouse_location_id' => null,
				'quantity' => $product->pivot->quantity,
				'cost_per_unit' => $cost_per_unit,
			]);
		}

		return redirect()->route('warehouse_inbounds.show', $inbound->id)->with('success', "Inbound request created successfully.");
	}


	public function inputInboundtoWarehouse($inbound) {
		$inbound->status = 'INB_PROCESS';
		$inbound->save();

		return redirect()->route('warehouse_inbounds.show', $inbound->id)->with('success', "Inbound {$inbound->id} is on Process :)");
	}

	public function completeInbound($inbound) {
		$inbound->status = 'INB_COMPLETED';
		$inbound->save();

		// input inbound products to warehouse & inventory
		$this->inputInboundProductsToWarehouse($inbound);

		return redirect()->route('warehouse_inbounds.show', $inbound->id)->with('success', "Inbound {$inbound->id} is Completed :D");
	}

	public function inputInboundProductsToWarehouse($inbound) {
		$inbound_products = $inbound->inbound_products;

		$inventory_value = 0;
		foreach($inbound_products as $inbound_product){
			$inventory_movement = InventoryMovement::create([
				'product_id' => $inbound_product->product_id,
				'warehouse_id' => $inbound->warehouse_id,
				'warehouse_location_id' => $inbound_product->warehouse_location_id,
				'quantity' => $inbound_product->quantity,
				'cost_per_unit' => $inbound_product->cost_per_unit,
				'employee_id' => $inbound->employee_id,
				'source_type' => 'INB',
				'source_id' => $inbound->id,
			]);

			$inventory_movement->postMovement();

			$inventory_value += $inbound_product->quantity * $inbound_product->cost_per_unit;
		}

		// create journal
		$journalService = app(JournalEntryService::class);
		$journalService->addJournalEntry([
			'source_type' => 'INB',
			'source_id' => $inbound->id,
			'date' => date('Y-m-d'),
			'type' => 'INB',
			'description' => 'Inbound',
			'total' => $inventory_value,
			'created_by' => $inbound->employee_id,
		], [
			[
				'account_id' => get_company_setting('comp.account_inventories'),                  // inventory
				'debit' => $inventory_value,
			],
			[
				'account_id' => get_company_setting('comp.account_downpayment_supplier'),		// uang muka supplier
				'credit' => $inventory_value,
				'notes' => 'uang muka supplier',
			],
		]);
	}
	
}
