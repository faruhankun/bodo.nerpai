<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Store\StoreInbound;
use App\Models\Store\StoreInboundProduct;
use App\Models\Store\StoreEmployee;
use App\Models\Store\StoreProduct;
use App\Models\Store\StoreInventory;

use App\Models\Company\Shipment;
use App\Models\Company\Store;
use App\Models\Company\ShipmentConfirmation;

class StoreInboundController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $store = Store::findOrFail(session('company_store_id'));
        $inbounds = StoreInbound::with('shipment_confirmation', 'store')
								->where('store_id', $store->id)
								->orderBy('created_at', 'desc')->get();
		$shipments_incoming = Shipment::where('consignee_type', 'ST')
										->where('consignee_id', $store->id)
										->orderBy('created_at', 'desc')->get();
        return view('store.store_inbounds.index', compact('inbounds', 'shipments_incoming'));
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $inbound = StoreInbound::with('store_inbound_products', 'shipment_confirmation')->findOrFail($id);

        return view('store.store_inbounds.show', compact('inbound'));
    }



    public function handleAction(Request $request, $inbounds, $action) {
		if($inbounds != '0') 
			$inbound = StoreInbound::findOrFail($inbounds);

		switch ($action) {
			case 'INB_REQUEST':
				// get shipment confirmation
				return $this->createInboundFromShipment($request->shipment_confirmation);
			case 'INB_PROCESS':
				return $this->inputInboundtoStore($inbound);
				break;
			case 'INB_COMPLETED':
				return $this->completeInbound($inbound);
				break;
			default:
				abort(404);
		}

		return redirect()->route('store_inbounds.index')->with('success', "Inbound request {$inbound->id} updated successfully.");
	}

    public function createInboundFromShipment($shipment_confirmation) {
		$shipment_confirmation = ShipmentConfirmation::with('products', 'shipment')->findOrFail($shipment_confirmation);
		$products = $shipment_confirmation->products;
		$shipment = $shipment_confirmation->shipment;
        $source = $shipment->transaction;
        $store_employee = StoreEmployee::where('store_id', $shipment->consignee_id)
                                        ->where('employee_id', $shipment_confirmation->employee_id)
                                        ->first();
        
                                        // cost per unit
        $source_cost_per_unit = [];
        if($shipment->transaction_type == 'OUTB') {
            $items = $source->items;
            $source_cost_per_unit = $items->mapWithKeys(function ($item) {
                return [$item->item->product_id => $item->cost_per_unit];
            });
        }



		$inbound = StoreInbound::create([
			'store_id' => $shipment->consignee_id,
			'shipment_confirmation_id' => $shipment_confirmation->id,
			'store_employee_id' => $store_employee->id,
			'status' => 'INB_REQUEST',
			'date' => date('Y-m-d'),
		]);

        $storeProductMap = StoreProduct::whereIn('product_id', $products->pluck('id'))
                                        ->pluck('id', 'product_id');

        
        $inbound_product = [];
		foreach($products as $product){
            if(!isset($storeProductMap[$product->id])){
                $store_product = StoreProduct::create([
                    'store_id' => $shipment->consignee_id,
                    'product_id' => $product->id,
                    'store_price' => $product->price,
                ]);
                $storeProductMap[$product->id] = $store_product->id;
            }
            $inbound_product[] = [
                'store_inbound_id' => $inbound->id,
                'store_product_id' => $storeProductMap[$product->id],
                'quantity' => $product->pivot->quantity,
                'store_location_id' => null,
                'cost_per_unit' => $source_cost_per_unit[$product->id] ?? 0,
                'total_cost' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];
		}
        StoreInboundProduct::insert($inbound_product);

		return redirect()->route('store_inbounds.show', $inbound->id)->with('success', "Inbound request {$inbound->id} created successfully.");
	}

    public function inputInboundtoStore($inbound) {
		$inbound->status = 'INB_PROCESS';
		$inbound->save();

		return redirect()->route('store_inbounds.show', $inbound->id)->with('success', "Inbound {$inbound->id} is on Process :)");
	}

	public function completeInbound($inbound) {
		// input inbound products to warehouse & inventory
		$this->inputInboundProductsToStore($inbound);


        $inbound->status = 'INB_COMPLETED';
		$inbound->save();

		return redirect()->route('store_inbounds.show', $inbound->id)->with('success', "Inbound {$inbound->id} is Completed :D");
	}

    public function inputInboundProductsToStore($inbound) {
		$inbound_products = $inbound->store_inbound_products;
		foreach($inbound_products as $inbound_product){
			$inventory = StoreInventory::create([
				'store_id' => $inbound->store_id,
				'store_product_id' => $inbound_product->store_product_id,
				'store_location_id' => $inbound_product->store_location_id,
				'quantity' => $inbound_product->quantity,
				'cost_per_unit' => $inbound_product->cost_per_unit,
			]);
		}
	}
}
