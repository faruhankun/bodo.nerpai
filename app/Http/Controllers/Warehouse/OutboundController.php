<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Warehouse\Outbound;
use App\Models\Warehouse\OutboundItem;
use App\Models\Company\Warehouse;
use App\Models\Company\Shipment;

use App\Models\Company\Product;
use App\Models\Sale;
use App\Models\Company\Inventory\Inventory;
use App\Models\InventoryMovement;



use Illuminate\Http\Request;

class OutboundController extends Controller
{
    public function index()
    {
		$warehouse = Warehouse::findOrFail(session('company_warehouse_id'));
        $outbounds = Outbound::with('source')
				->where('warehouse_id', $warehouse->id)
				->orderBy('created_at', 'desc')->get();
		$shipments_outgoing = Shipment::where('shipper_type', 'WH')
				->where('shipper_id', $warehouse->id)
				->orderBy('created_at', 'desc')->get();

		return view('warehouse.warehouse_outbounds.index', compact('outbounds', 'shipments_outgoing'));
    }

	public function show($id)
	{
		$warehouse_id = session('company_warehouse_id');
		$outbound_action_allowed = false;
		
		if($warehouse_id)
		{
			$outbound_action_allowed = true;
		}

		$outbound = Outbound::with(['source', 'shipments', 'items',])->findOrFail($id);


		return view('warehouse.warehouse_outbounds.show', compact('outbound', 'outbound_action_allowed'));
	}


	public function handleAction(Request $request, $warehouse_outbounds, $action) {
		$outbound = Outbound::findOrFail($warehouse_outbounds);

		switch ($action) {
			case 'OUTB_PROCESS':
				return $this->acceptOutbound($outbound);
			case 'OUTB_IN_TRANSIT':
				return $this->createShipmentFromOutbound($outbound);
				break;
			case 'OUTB_COMPLETED':
				return $this->completeOutbound($outbound);
				break;
			default:
				abort(404);
		}

		return redirect()->route('warehouse_outbounds.show', $outbound->id)->with('success', "Outbound request {$outbound->number} updated successfully.");
	}


	public function acceptOutbound($outbound) 
	{
		$outbound->status = 'OUTB_PROCESS';
		$outbound->save();
	
		return redirect()->route('warehouse_outbounds.show', $outbound->id)->with('success', "Outbound request {$outbound->number} updated successfully.");
	}


	public function createShipmentFromOutbound($outbound) 
	{
		$shipment = Shipment::create([
			'shipper_type' => 'WH',
			'shipper_id' => $outbound->warehouse_id,
			'origin_address' => $outbound->warehouse->address,
			'consignee_type' => $outbound->source->consignee_type,
			'consignee_id' => $outbound->source->consignee_id,
			'destination_address' => $outbound->source->consignee->address,
			'transaction_type' => 'OUTB',
			'transaction_id' => $outbound->id,
			'courier_id' => $outbound->source->courier_id,
			'ship_date' => now()->format('Y-m-d'),
			'status' => 'SHP_IN_TRANSIT',
		]);
		$shipment->generateShipmentNumber();
		$shipment->save();



		// update inventory quantity move to in_transit_quantity
		$this->updateOutboundProductstoInventory($outbound);

		

		$outbound->status = 'OUTB_IN_TRANSIT';
		$outbound->save();
	
		return redirect()->route('shipments.edit', $shipment->id)->with('success', "Shipment {$shipment->shipment_number} created successfully");
	}

	public function completeOutbound($outbound) 
	{
		$outbound->status = 'OUTB_COMPLETED';
		$outbound->save();
	
		return redirect()->route('warehouse_outbounds.show', $outbound->id)->with('success', "Outbound {$outbound->number} updated successfully.");
	}


	public function updateOutboundProductstoInventory($outbound) {
		$outbound_items = $outbound->items;

		// update inventory quantity to in_transit_quantity
		foreach($outbound_items as $item) {
			$item->inventory->in_transit_quantity += $item->quantity;
			$item->inventory->quantity -= $item->quantity;
			$item->inventory->save();
		}
	}
}
