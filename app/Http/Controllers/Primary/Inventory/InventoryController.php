<?php

namespace App\Http\Controllers\Primary\Inventory;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Yajra\DataTables\Facades\DataTables;

use App\Models\Primary\Inventory;
use App\Models\Primary\Space;

class InventoryController extends Controller
{
    public function index()
    {
        $space_id = session('space_id') ?? null;
        if(is_null($space_id)){
            abort(403);
        }

        return view('primary.inventory.supplies.index');
    }



    public function getSuppliesData(){
        $space_id = session('space_id') ?? null;
        if(is_null($space_id)){
            abort(403);
        }

        $supplies = Inventory::with('type', 'item', 'tx_details')
                            ->where('model_type', 'SUP');

        if($space_id){
            $space = Space::findOrFail($space_id);

            $spaceIds = $space->AllChildren()->pluck('id')->toArray();
            $spaceIds = array_merge($spaceIds, [$space_id]);

            $supplies = $supplies->where('space_type', 'SPACE')
                                    ->whereIn('space_id', $spaceIds);
        } 

        return DataTables::of($supplies)
            ->addColumn('getSupplyBalance', function ($data) {
                // return $data->getSupplyBalance();
                return 0;
            })
            ->addColumn('item_display', function ($data) {
                $item_display = ($data->item_type ?? '?') . ' : ' . ($data->item->name ?? '?');
                return $item_display;
            })
            ->addColumn('actions', function ($data) {
                $route = 'supplies';
                
                $actions = [
                    'show' => 'modal',
                    'show_modal' => 'primary.inventory.supplies.show',
                ];

                return view('components.crud.partials.actions', compact('data', 'route', 'actions'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }
}
