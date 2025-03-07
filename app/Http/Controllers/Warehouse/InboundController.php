<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Company\Inbound;
use App\Models\Company\InboundProducts;
use App\Models\Company\Purchase;
use App\Models\User;
use App\Models\Location;
use App\Models\Inventory;
use App\Models\InventoryHistory;
use App\Models\Company\Shipment;
use App\Models\Company\Warehouse;
use App\Models\Company\ShipmentConfirmation;

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


	// Update method to handle form submission
    public function update(Request $request, $id)
    {
        $request->validate([
			'status' => 'required',
			'verified_by' => 'nullable|exists:users,id',
			'notes' => 'nullable|string',
			'received_quantities' => 'array',
			'received_quantities.*' => 'integer|min:0',
			'arrival_date' => 'nullable|date',  // Add this line
		]);


        $inboundRequest = Inbound::findOrFail($id);
		
		// Handle checking quantities action
		if ($request->action === 'check_quantities') {
			$hasDiscrepancy = false;
			$receivedQuantities = $request->input('received_quantities', []);

			// Check for discrepancies
			foreach ($receivedQuantities as $productId => $receivedQuantity) {
				$requestedQuantity = $inboundRequest->requested_quantities[$productId] ?? 0;
				if ($requestedQuantity != $receivedQuantity) {
					$hasDiscrepancy = true;
					break;
				}
			}

			// Set status based on discrepancy
			if ($hasDiscrepancy) {
				$inboundRequest->status = 'Quantity Discrepancy';
				$inboundRequest->notes = 'Quantity discrepancy detected. Awaiting purchase team decision.';
			} else {
				$inboundRequest->status = 'Ready to Complete';
				$inboundRequest->notes = 'Quantities match; ready for completion.';
			}

			$inboundRequest->received_quantities = $receivedQuantities;
			$inboundRequest->save();

			return redirect()->route('warehouse_inbounds.show', $inboundRequest->id)
				->with('success', 'Quantities checked successfully.');
		}
		
        $inboundRequest->update([
			'status' => $request->status,
			'verified_by' => $request->verified_by,
			'notes' => $request->notes,
			'received_quantities' => $request->input('received_quantities', []),
			'arrival_date' => $request->arrival_date,
		]);

		// Check if arrival_date is set and status is In Transit, then update status to Received - Pending Verification
		if ($request->arrival_date && $inboundRequest->status == 'In Transit') {
			$inboundRequest->status = 'Received - Pending Verification';
			$inboundRequest->save();
		}

		// Check overall status of all inbound requests for this purchase
		$this->updatePurchaseStatus($inboundRequest->purchase_order_id);

        return redirect()->route('warehouse_inbounds.index')
            ->with('success', 'Inbound request updated successfully.');
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

		$inbound = Inbound::create([
			'warehouse_id' => $shipment->consignee_id,
			'shipment_confirmation_id' => $shipment_confirmation->id,
			'employee_id' => $shipment_confirmation->employee_id,
			'status' => 'INB_REQUEST',
			'inbound_date' => date('Y-m-d'),
		]);

		foreach($products as $product){
			$inbound_product = InboundProducts::create([
				'inbound_id' => $inbound->id,
				'product_id' => $product->id,
				'quantity' => $product->pivot->quantity,
				'warehouse_location_id' => '1',
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
		foreach($inbound_products as $inbound_product){
			$inventory = Inventory::create([
				'warehouse_id' => $inbound->warehouse_id,
				'product_id' => $inbound_product->product_id,
				'warehouse_location_id' => $inbound_product->warehouse_location_id,
				'quantity' => $inbound_product->quantity,
				'cost_per_unit' => 0.5 * $inbound_product->product->price,
			]);
		}
	}
}
