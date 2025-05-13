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



    public function store(Request $request)
    {
        $space_id = session('space_id') ?? null;

        try {
            $validated = $request->validate([
                'ivt_id' => 'required',
                'status' => 'required|string|max:50',
                'notes' => 'nullable',
            ]);

            if($space_id){
                $validated['space_type'] = 'SPACE';
                $validated['space_id'] = $space_id;
            }

            $ivt = Inventory::findOrFail($validated['ivt_id']);
            $validated['name'] = $ivt->name;
            $validated['code'] = $ivt->code;
            $validated['sku'] = $ivt->sku;
            $validated['status'] = $validated['status'];
            $validated['notes'] = $validated['notes'];

            $validated += [
                'model_type' => 'SUP',
                'ivt_type' => 'ITM',
                'parent_type' => 'IVT',
            ];

            $ivt = Inventory::create($validated);

            return redirect()->route('supplies.index')->with('success', "Supply {$ivt->name} created successfully.");
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }


    public function getSuppliesData(){
        $space_id = session('space_id') ?? null;
        if(is_null($space_id)){
            abort(403);
        }

        $supplies = Inventory::with('type', 'ivt', 'tx_details')
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
            ->addColumn('ivt_display', function ($data) {
                $ivt_display = ($data->ivt_type ?? '?') . ' : ' . ($data->ivt->name ?? '?');
                return $ivt_display;
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



    public function searchSupply(Request $request)
    {
        $search = $request->q;

        $space_id = $request['space_id'] ?? (session('space_id') ?? null);
        if(is_null($space_id)){
            abort(403);
        }

        $ivts = Inventory::where(function ($query) use ($search) {
            $query->where('name', 'like', "%$search%")
                ->orWhere('code', 'like', "%$search%")
                ->orWhere('sku', 'like', "%$search%")
                ->orWhere('notes', 'like', "%$search%")
                ->orWhere('id', 'like', "%$search%");
        })
            ->where('model_type', 'SUP')
            ->where('space_type', 'SPACE')
            ->where('space_id', $space_id)
            ->orderBy('id', 'desc')
            ->limit(50) // limit hasil
            ->get()
            ->map(function ($ivt) {
                return [
                    'id' => $ivt->id,
                    'text' => "{$ivt->id} - {$ivt->sku} - {$ivt->name} qty: {$ivt->balance} : {$ivt->notes}",
                ];
            });

        return response()->json($ivts);
    }
}
