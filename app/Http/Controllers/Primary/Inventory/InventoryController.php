<?php

namespace App\Http\Controllers\Primary\Inventory;

use App\Http\Controllers\Controller;
use App\Services\Primary\Basic\EximService;

use Yajra\DataTables\Facades\DataTables;

use App\Models\Primary\Inventory;
use App\Models\Primary\Space;
use App\Models\Primary\Item;
use App\Models\Primary\Transaction;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class InventoryController extends Controller
{
    protected $eximService;

    protected $import_columns = [
        'item_code', 
        'item_sku', 
        'supplies_name', 
        'supplies_stock', 
        'cost_per_unit', 
        'expire_date', 
        'notes'
    ];



    public function __construct(EximService $eximService)
    {
        $this->eximService = $eximService;
    }



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
                'item_id' => 'required',
                'status' => 'required|string|max:50',
                'notes' => 'nullable',
            ]);

            if($space_id){
                $validated['space_type'] = 'SPACE';
                $validated['space_id'] = $space_id;
            }

            $item = Item::findOrFail($validated['item_id']);
            $validated['name'] = $item->name;
            $validated['code'] = $item->code;
            $validated['sku'] = $item->sku;
            $validated['cost_per_unit'] = $item->cost;
            $validated['status'] = $validated['status'];
            $validated['notes'] = $validated['notes'];

            $validated += [
                'model_type' => 'SUP',
                'item_type' => 'ITM',
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
            ->addColumn('space_display', function ($data) {
                $space_display = ($data->space->name ?? '?');
                return $space_display;
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
                    'text' => "{$ivt->id} - {$ivt->sku} - {$ivt->name} qty: {$ivt->balance} x {$ivt->cost_per_unit} : {$ivt->notes}",
                    'cost_per_unit' => $ivt->cost_per_unit,
                ];
            });

        return response()->json($ivts);
    }




    // Export Import
    public function importTemplate(){
        $response = $this->eximService->exportCSV(['filename' => 'supplies_import_template.csv'], $this->import_columns);

        return $response;
    }


    public function importData(Request $request)
    {
        $space_id = session('space_id') ?? null;

        // try {
        //     $validated = $request->validate([
        //         'file' => 'required|mimes:csv,txt'
        //     ]);

        //     $file = $validated['file'];
        //     $data = [];
        //     $failedRows = [];
        //     $requiredHeaders = ['item_sku', 'supplies_stock', 'name'];

        //     // Read the CSV into an array of associative rows
        //     $data = $this->eximService->convertCSVtoArray($file, ['requiredHeaders' => $requiredHeaders]);


        //     // input
        //     foreach ($data as $i => $row) {
        //         try {
        //             // skip if no code or name
        //             if (empty($row['sku']) || empty($row['name'])) {
        //                 throw new \Exception('Missing required field: sku or name');
        //             }

        //             $item = Item::where('code', $row['code'])
        //             ->orWhere('sku', $row['sku'])
        //             ->orWhere('name', $row['name'])
        //             ->first();

        //             $payload = [
        //                 'code' => $row['code'],
        //                 'sku' => $row['sku'],
        //                 'name' => $row['name'],
        //                 'price' => $row['price'] ?? 0,
        //                 'cost' => $row['cost'] ?? 0,
        //                 'weight' => $row['weight (gram)'] ?? 0,
        //                 'notes' => $row['notes'] ?? null,
        //             ];

        //             $payload['space_type'] = 'SPACE';
        //             if($space_id) 
        //                 $payload['space_id'] = $space_id;

        //             if ($item) {
        //                 $item->update($payload);
        //             } else {
        //                 Item::create($payload);
        //             }
        //         } catch (\Throwable $e) {
        //             $row['row'] = $i + 2; // +2 karena array dimulai dari 0 dan +1 untuk header CSV
        //             $row['error'] = $e->getMessage();
        //             $failedRows[] = $row;
        //         }
        //     }


        //     // Jika ada row yang gagal, langsung return CSV dari memory
        //     if (count($failedRows) > 0) {
        //         $filename = 'failed_import_' . now()->format('Ymd_His') . '.csv';
                
        //         $this->eximService->exportCSV(['filename' => $filename], $failedRows);
        //     }

        //     return redirect()->route('supplies.index')->with('success', 'CSV uploaded and processed Successfully!');
        // } catch (\Throwable $th) {
        //     return back()->with('error', 'Failed to import csv. Please try again.' . $th->getMessage());
        // }

        return back()->with('error', 'Under Construction');
    }


    public function exportData(){
        return back()->with('error', 'Under Construction');
    }




    
    // Summaries
    public function summary(Request $request)
    {
        $space_id = session('space_id') ?? null;
        if(is_null($space_id)){
            abort(403);
        }

        $supplies = Inventory::with('type', 'item', 'tx_details')
                            ->where('model_type', 'SUP');

        $space = Space::findOrFail($space_id);
        $spaces = $space->allChildren();
        $spaces = $spaces->prepend($space);


        // generate data by date
        $date = $request->date ?? now()->format('Y-m-d');
        $date_time = Carbon::parse($date)->endOfDay();
        $txs = Transaction::with('input', 'type', 'details', 'details.detail') 
                            ->where('model_type', 'JS')
                            ->where('space_type', 'SPACE')
                            ->whereIn('space_id', $spaces->pluck('id')->toArray())
                            ->where('sent_time', '<=', $date_time)
                            ->orderBy('sent_time', 'asc');
        
        $txs = $txs->get();

        return view('primary.inventory.supplies.summary', compact('txs', 'spaces'));
    }
}
