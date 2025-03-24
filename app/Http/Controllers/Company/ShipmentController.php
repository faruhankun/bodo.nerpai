<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Company\Shipment;
use App\Models\Company\ShipmentConfirmation;
use App\Models\Company\Courier;

class ShipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shipments = Shipment::orderBy('updated_at', 'desc')->get();
        return view('company.shipments.index', compact('shipments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $shipment = Shipment::with('shipper', 'consignee', 'transaction', 'courier', 'shipment_confirmations')
                            ->findOrFail($id);
    
        $shipment_confirm_allowed = false;
        $consignee_type = $shipment->consignee_type;
        $consignee_id = $shipment->consignee_id;
        $layout = session('layout');

        if($consignee_type == 'ST' && $layout == 'store') {
            if($consignee_id == session('company_store_id')) {
                $shipment_confirm_allowed = true;
            }
        } else if($consignee_type == 'WH' && $layout == 'warehouse') {
            if($consignee_id == session('company_warehouse_id')) {
                $shipment_confirm_allowed = true;
            }
        } else if($consignee_type == 'CUST' && $layout == 'company') {
            $shipment_confirm_allowed = true;
        }

        $shipment_completed_allowed = false;
        $shipper_type = $shipment->shipper_type;
        $shipper_id = $shipment->shipper_id;

        if($shipper_type == 'WH' && $layout == 'warehouse') {
            if($shipper_id == session('company_warehouse_id')) {
                $shipment_completed_allowed = true;
            }   
        }

        return view('company.shipments.show', compact('shipment', 
            'shipment_completed_allowed', 
            'shipment_confirm_allowed'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $shipment = Shipment::findOrFail($id);
        $couriers = Courier::all();

        return view('company.shipments.edit', compact('shipment', 'couriers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'courier_id' => 'required|exists:couriers,id',
            'ship_date' => 'required|date',
            'tracking_number' => 'required|string|max:255',
            'shipping_fee' => 'required|numeric',
            'payment_rules' => 'nullable|string|max:255',
            'packing_quantity' => 'required|integer',
            'notes' => 'nullable|string',
        ]);

        $shipment = Shipment::findOrFail($id);
        $shipment->update($validated);
        $shipment->generateShipmentNumber();
        $shipment->save();

        $referrer = $request->referrer;
        return redirect()->to($referrer ?? route('shipments.index'))->with('success', "Shipment {$shipment->shipment_number} updated successfully.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


    
    public function confirm($id){
        $shipment_confirmation = ShipmentConfirmation::with('products', 'shipment')
                                ->findOrFail($id);
        $shipment = $shipment_confirmation->shipment;
        $products = $shipment_confirmation->products;

        return view('company.shipments.confirm', compact('shipment_confirmation', 'shipment', 'products'));
    }

    public function confirm_show($id){
        $shipment_confirmation = ShipmentConfirmation::with('products', 'shipment')
                                ->findOrFail($id);
        $shipment = $shipment_confirmation->shipment;
        $products = $shipment_confirmation->products;

        return view('company.shipments.confirm-show', compact('shipment_confirmation', 'shipment', 'products'));
    }

    public function confirm_update(Request $request, $id){
        $validated = $request->validate([
            'received_time' => 'required|date',
            'consignee_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $shipment_confirmation = ShipmentConfirmation::findOrFail($id);
        $shipment_confirmation->update($validated);
        $shipment_confirmation->save();

        $referrer = $request->referrer;
        if($referrer == route('shipments.confirm', $shipment_confirmation->id)){
            $referrer = "";     // reset it
        }
        return redirect()->to($referrer ?? route('shipments.index'))->with('success', "Shipment Confirmation ID:{$shipment_confirmation->id} updated successfully.");
    }



    public function handleAction(Request $request, $shipments, $action){
		$shipment = Shipment::findOrFail($shipments);

		switch($action){
			case 'SHP_DELIVERY_CONFIRMED':
				return $this->inputDeliveryConfirmation($shipment);
				break;

            case 'SHP_COMPLETED':
                return $this->completeShipment($shipment);
			
                default:
				abort(404);
		}

		return redirect()->route('shipments.index')->with('success', "Shipment {$shipment->shipment_number} updated successfully.");
	}

    public function inputDeliveryConfirmation($shipment){
        $shipment->status = 'SHP_DELIVERY_CONFIRMED';
        $shipment->save();

        $employee = session('employee');

        $shipment_confirmation = ShipmentConfirmation::create([
            'shipment_id' => $shipment->id,
            'employee_id' => $employee->id,
            'received_time' => now()->format('Y-m-d H:i:s'),
        ]);

        // pre input produk
        $shipment_source = $shipment->transaction_type;
        $products = $shipment->transaction->products;
        if($shipment_source == 'OUTB'){
            $items = $shipment->transaction->items;

            if($items->isNotEmpty()){
                foreach($items as $item){
                    $shipment_confirmation->products()->attach($item->item->product_id, [
                        'quantity' => $item->quantity,
                        'condition' => 'SC_OK',
                    ]);
                }
            }
        } else if($products->isNotEmpty()){
            foreach($products as $product){
                $shipment_confirmation->products()->attach($product->id, [
                    'quantity' => $product->pivot->quantity,
                    'condition' => 'SC_OK',
                ]);
            }
        }

        return redirect()->route('shipments.confirm', $shipment_confirmation->id)->with('success', "Shipment Confirmation {$shipment->shipment_number} created successfully.");
    }

    public function completeShipment($shipment)
    {
        // feedback to transaction

        $shipment->status = 'SHP_COMPLETED';
        $shipment->save();

        return redirect()->route('shipments.index')->with('success', "Shipment {$shipment->shipment_number} updated successfully.");
    }
}
