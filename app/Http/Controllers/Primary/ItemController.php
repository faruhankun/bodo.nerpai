<?php

namespace App\Http\Controllers\Primary;
use App\Http\Controllers\Controller;

use App\Models\Primary\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;

use Yajra\DataTables\Facades\DataTables;

use App\Models\Primary\Inventory;
use App\Models\Primary\Space;
use App\Models\Primary\Transaction;

use Carbon\Carbon;

use App\Services\Primary\Basic\EximService;
use App\Services\Primary\Transaction\TradeService;



class ItemController extends Controller
{
    public function __construct(EximService $eximService
                                , TradeService $tradeService){
        $this->eximService = $eximService;
        $this->tradeService = $tradeService;
    }


    // get data
    public function getData(Request $request){
        $space_id = get_space_id($request);

        $space = Space::findOrFail($space_id);
        $spaces = array($space_id, $space->parent_id);

        $query = Item::with('inventories')
                    ->whereIn('space_id', $spaces);



        // Limit
        $limit = $request->get('limit');
        if($limit){
            if($limit != 'all'){
                $query->limit($limit);
            } 
        } else {
            $query->limit(50);
        }

        
        // Search
        $keyword = $request->get('q');
        if($keyword){
            $query->where(function($q) use ($keyword){
                $q->where('name', 'like', "%{$keyword}%")
                ->orWhere('id', 'like', "%{$keyword}%")
                ->orWhere('code', 'like', "%{$keyword}%")
                ->orWhere('notes', 'like', "%{$keyword}%")
                ->orWhere('sku', 'like', "%{$keyword}%");
            });
        }



        // order by id desc by default
        $orderby = $request->get('orderby');
        $orderdir = $request->get('orderdir');
        if($orderby && $orderdir){
            $query->orderBy($orderby, $orderdir);
        } else {
            $query->orderBy('id', 'desc');
        }



        // return result
        return DataTables::of($query)->make(true);
    }   



    public function updateInventoryToChildren(Request $request){
        try {
            $validated = $request->validate([
                'id' => 'required|exists:items,id',
            ]);

            $item = Item::with('inventories', 'space')->findOrFail($validated['id']);

            $space_and_children = $item->space->spaceAndChildren()->pluck('id')->toArray();
            $space_with_inventories = $item->inventories()->pluck('space_id')->toArray();


            // create inventory to children who don't have it
            foreach ($space_and_children as $space_id) {
                if(!in_array($space_id, $space_with_inventories)){
                    $ivt = [
                        'item_type' => 'ITM',
                        'item_id' => $item->id,
                        
                        'space_type' => $item->space_type,
                        'space_id' => $space_id,

                        'name' => $item->name,
                        'code' => $item->code,
                        'sku' => $item->sku,
                        'cost_per_unit' => $item->cost,

                        'status' => $item->status,
                        'notes' => $item->notes,

                        'model_type' => 'SUP',
                        'parent_type' => 'IVT',
                    ];

                    $supply = Inventory::create($ivt);
                }
            }


            return response()->json([
                'data' => array($item),
                'success' => true,
                'message' => 'Supplies for item have been updated successfully',
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'success' => false, 'data' => []], 500);
        }
    }



    // Summaries
    public $summary_types = [
        'itemflow' => 'Arus Barang',
    ];

    public function summary(Request $request)
    {
        $space_id = get_space_id($request);
        $request_source = get_request_source($request);

        $space = Space::findOrFail($space_id);
        $spaces = $space->spaceAndChildren();



        // generate data by date
        $validated = $request->validate([
            'summary_type' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $start_date = $validated['start_date'] ?? null;
        $end_date = $validated['end_date'] ?? now()->format('Y-m-d');

        $end_time = Carbon::parse($end_date)->endOfDay();
        
        $txs = Transaction::with('input', 'type', 'details', 'details.detail') 
                            ->where('model_type', 'TRD')
                            ->where('space_type', 'SPACE')
                            ->whereIn('space_id', $spaces->pluck('id')->toArray())
                            ->where('sent_time', '<=', $end_time)
                            ->orderBy('sent_time', 'asc');

        if(!is_null($start_date)){
            $start_time = Carbon::parse($start_date)->startOfDay();
            $txs = $txs->where('sent_time', '>=', $start_time);
        }
        
        $txs = $txs->get();



        // generate data by item
        $data = collect();
        $data->summary_types = $this->summary_types;
        $list_model_types = $this->tradeService->model_types ?? [];

        $data->items_list = Item::all()->keyBy('id');
        $data = $this->getSummaryData($data, $txs, $spaces, $validated, $space_id);



        if($request_source == 'api'){
            $data_summary = [];
            if(isset($validated['summary_type']) && isset($data->{$validated['summary_type']})){
                $data_summary = $data->{$validated['summary_type']};
            }

            $spaces_data = $spaces->toArray();


            // itemflow
            if($validated['summary_type'] == 'itemflow'){
                $itemflow = $this->getSummaryItemflow($txs);
                $data_summary = $itemflow->toArray();
            }


            return response()->json([
                'data' => $data_summary,
                'summary_types' => $this->summary_types,
                'spaces' => $spaces_data,
                'input' => $validated,
                'list_model_types' => $list_model_types,
                'success' => true,
            ]);
        }

        return view('primary.items.summary', compact('data', 'txs', 'spaces', 'list_model_types'));
    }

    

    public function getSummaryItemflow($txs){
        $itemflow = collect();

        $txs_per_date = $txs->groupBy('sent_time');

        $space_supply = 0;
        
        
        $per_date_change_initial = [];
        $list_model_types = $this->tradeService->model_types ?? [];
        foreach($list_model_types as $model_type){
            $per_date_change_initial[$model_type['id']] = 0;
        }



        foreach($txs_per_date as $end_date => $txs){
            $per_date_change = $per_date_change_initial;
            
            $per_date = [
                'date' => $end_date,
                'change' => 0,
                'balance' => $space_supply,
            ];

            foreach($txs as $tx){
                foreach($tx->details as $detail){
                    // tx
                    $per_date_change[$detail->model_type] += $detail->quantity * $detail->price * (1 - $detail->discount);
                }
            }

            $per_date['change'] += array_sum($per_date_change);
            $per_date['balance'] += $per_date['change'];
            $space_supply = $per_date['balance'];

            $per_date = array_merge($per_date, $per_date_change);
            $itemflow->push($per_date);
        }

        return $itemflow;
    }


    public function getSummaryData($data, $txs, $spaces, $validated, $space_id = null){
        $summary_type = $validated['summary_type'] ?? null;
        if(is_null($summary_type)){
            return $data;
        }

        // Transaction;
        $spaces_per_id = $spaces->groupBy('id');
        $txs_per_space = $txs->groupBy('space_id');                
        $items_data = collect();
        $items_data_all = [];

        $item_data_initial = [];
        $list_model_types = $this->tradeService->model_types ?? [];
        foreach($list_model_types as $model_type){
            $item_data_initial[$model_type['id']]['quantity'] = 0;
            $item_data_initial[$model_type['id']]['subtotal'] = 0;
        }



        foreach($txs_per_space as $id => $txs){
            $txs_per_date = $txs->groupBy('sent_time');

            $items_per_space = collect();
            $items = [];

            foreach($txs_per_date as $end_date => $txs){
                foreach($txs as $tx){
                    foreach($tx->details as $detail){
                        // item
                        // if move not included
                        $item = $data->items_list[$detail->detail_id] ?? null;
                        if(!isset($items[$item->id])){
                            $items[$item->id] = $item_data_initial;
                            $items[$item->id]['item'] = $item;
                        }

                        $items[$item->id][$detail->model_type]['quantity'] += $detail->quantity;
                        $items[$item->id][$detail->model_type]['subtotal'] += $detail->quantity * $detail->price * (1 - $detail->discount);
                    }
                }
            }

            $items_data->put($id, $items);
            $items_data_all = array_merge($items_data_all, $items);
        }

        $data->itemflow = collect([$space_id => $items_data_all]);

        return $data;
    }




    public function index()
    {
        return view('primary.items.index');
    }



    public function show(Request $request, $id)
    {
        $data = Item::with('transaction_details')->findOrFail($id);

        return view('primary.items.show', compact('data'));
    }



    public function store(Request $request)
    {
        $space_id = get_space_id($request);

        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'nullable|string|max:255',
                'sku' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
            ]);

            $validatedData['space_type'] = 'SPACE';
            $validatedData['space_id'] = $space_id;


            $item = Item::create($validatedData);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'success' => false, 'data' => []], 500);
        }

        return response()->json([
            'data' => array($item),
            'success' => true,
            'message' => 'Item created successfully',
        ]);
    }
    


    public function update(Request $request, $id)
    {   
        $request_source = get_request_source($request);


        try {
            $validatedData = $request->validate([
                'code' => 'nullable|string|max:255',
                'name' => 'required|string|max:255',
                'sku' => 'nullable|string|max:255',
                'price' => 'nullable|numeric|min:0',
                'cost' => 'nullable|numeric|min:0',
                'weight' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',
                'description' => 'nullable|string',
                'status' => 'nullable|string',
            ]);
            
            $item = Item::findOrFail($id);
            $item->update($validatedData);
        } catch (\Throwable $th) {
            if($request_source == 'api'){
                return response()->json(['message' => $th->getMessage(), 'success' => false, 'data' => []], 500);
            }

            return back()->with('error', 'Something went wrong. Please try again. ' . $th->getMessage());
        }


        if($request_source == 'api'){
            return response()->json([
                'data' => array($item),
                'success' => true,
                'message' => 'Item updated successfully',
            ]);
        }

        return redirect()->route('items.index')->with('success', 'Item updated successfully');
    }



    public function destroy(Request $request, $id)
    {
        $request_source = get_request_source($request);

        $item = Item::findOrFail($id);
        
        // check related inventory
        if ($item->inventories()->exists()) {
            if($request_source == 'api'){
                return response()->json(['message' => 'Item has related inventory. Cannot delete.', 'success' => false, 'data' => []], 500);
            }

            return back()->with('error', 'Item has related inventory. Cannot delete.');
        }

        $item->delete();


        if($request_source == 'api'){
            return response()->json([
                'data' => array($item),
                'success' => true,
                'message' => 'Item deleted successfully',
            ]);
        }
        return redirect()->route('items.index')->with('success', 'Item deleted successfully');
    }



    public function getItemsData(Request $request){
        $space_id = get_space_id($request);

        $space = Space::findOrFail($space_id);
        $spaces_id = $space->spaceAndChildren()->pluck('id')->toArray();

        $space_and_parents = array($space_id, $space->parent_id);

        $items = Item::with('inventories')
                    ->whereIn('space_id', $space_and_parents);


        $items = $items->orderBy('id', 'asc');


        return DataTables::of($items)
            ->addColumn('supplies', function ($data) use ($spaces_id) {
                $ivts = $data->inventories->whereIn('space_id', $spaces_id);

                return '<table class="table-auto w-full">' .
                    '<tbody>' .
                    $ivts->map(function ($inv) {
                        $space_supplies = $inv->space?->variables()->where('key', 'space.setting.supplies')->first();
                        if ($space_supplies != null && $space_supplies->value == 0) {
                            return '';
                        }

                        return '<tr>' .
                            '<td class="border px-4 py-2">' . ($inv->space?->name ?? 'N/A') . '</td>' .
                            '<td class="border px-4 py-2 font-bold text-md text-blue-600">
                                <a href="javascript:void(0)" onclick="show_tx_modal(\'' . $inv->id . '\', \'' . $inv->sku . '\', \'' . $inv->name . '\')">
                                    ' . number_format($inv->balance) . ' pcs
                                </a></td>' .
                            '<td class="border px-4 py-2 font-bold text-md text-blue-600">
                                <a href="javascript:void(0)" onclick="edit_supply(\'' . $inv->id . '\', \'' . $inv->status . '\', \'' . $inv->notes . '\')">
                                    ' . 
                                    ($inv->notes == '' ? 'note?' : $inv->notes) 
                                    . 
                                '</a></td>' .
                            '</tr>';
                    })->implode('') .
                    
                    '<tr>' .
                        '<td class="border px-4 py-2">Total</td>' .
                        '<td class="border px-4 py-2">' . $ivts->sum('balance') . ' pcs</td>' .
                        '<td class="border px-4 py-2"></td>' .
                        '</tr>' .
                    
                    '</tbody>' .
                '</table>';
            })

            ->addColumn('actions', function ($data) {
                $route = 'items';
                
                $actions = [
                    'show' => 'modal',
                    'show_modal' => 'primary.items.show_modal',
                    'edit' => 'modal',
                    'delete' => 'button',
                ];

                return view('components.crud.partials.actions', compact('data', 'route', 'actions'))->render();
            })

            ->filter(function ($query) use ($request) {
                if ($request->has('search') && $request->search['value']) {
                    $search = $request->search['value'];

                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%")
                            ->orWhere('sku', "{$search}")
                            ->orWhere('id', 'like', "%{$search}%")
                            ->orWhere('notes', 'like', "%{$search}%")
                            ->orWhere('status', 'like', "%{$search}%")
                            ;
                    });
                }
            })
            
            ->rawColumns(['actions', 'supplies'])
            ->make(true);
    }



    public function searchItem(Request $request)
    {
        $space_id = get_space_id($request);
        $space_and_parents = array($space_id, Space::findOrFail($space_id)->parent_id);

        $search = $request->q;

        $items = Item::where(function ($query) use ($search) {
            $query->where('name', 'like', "%$search%")
                ->orWhere('code', 'like', "%$search%")
                ->orWhere('sku', 'like', "%$search%")
                ->orWhere('id', 'like', "%$search%");
        })

            ->whereIn('space_id', $space_and_parents)

            ->orderBy('id', 'asc')
            ->limit(50) // limit hasil
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'price' => $item->price,
                    'text' => "{$item->sku} - {$item->name} : {$item->notes}",
                    'sku' => $item->sku,
                    'name' => $item->name,
                    'weight' => $item->weight,
                ];
            });

        return response()->json($items);
    }



    public function importData(Request $request)
    {
        $request_source = get_request_source($request);
        $space_id = get_space_id($request);

        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'file' => 'required|mimes:csv,txt'
            ]);

            $file = $validated['file'];
            $data = [];
            $failedRows = [];
            $requiredHeaders = ['sku', 'name'];

            // Read the CSV into an array of associative rows
            if (($handle = fopen($file->getRealPath(), 'r')) !== FALSE) {
                $headers = fgetcsv($handle);

                // validasi header
                // dd($headers);
                foreach ($requiredHeaders as $header) {
                    if (!in_array($header, $headers)) {
                        DB::rollBack();

                        return back()->with('error', 'Invalid CSV file. Missing required header: ' . $header);
                    }
                }

                // Loop through the rows
                while (($row = fgetcsv($handle)) !== FALSE) {
                    $record = [];
                    foreach ($headers as $i => $header) {
                        $record[trim($header, " *")] = $row[$i] ?? null;
                    }
                    $data[] = $record;
                }
                fclose($handle);
            }


            // input
            foreach ($data as $i => $row) {
                try {
                    // skip if no code or name
                    if (empty($row['sku']) || empty($row['name'])) {
                        throw new \Exception('Missing required field: sku or name');
                    }

                    $item = Item::where('sku', $row['sku']);

                    if(isset($row['code']) && !empty($row['code'])) $item = $item->orWhere('code', $row['code']);
                    if($row['name'] && !empty($row['name'])) $item = $item->orWhere('name', $row['name']);
                    
                    $item = $item->first();


                    $payload = [
                        'code' => $row['code'] ?? ($item?->code ?? null),
                        'sku' => $row['sku'] ?? ($item?->sku ?? $row['name']),
                        'name' => $row['name'] ?? ($item?->name ?? $row['sku']),
                        'price' => $row['price'] ?? ($item?->price ?? 0),
                        'cost' => $row['cost'] ?? ($item?->cost ?? 0),
                        'weight' => $row['weight (gram)'] ?? ($item?->weight ?? 0),
                        'notes' => $row['notes'] ?? ($item?->notes ?? ''),
                    ];

                    $payload['space_type'] = 'SPACE';
                    if($space_id) 
                        $payload['space_id'] = $space_id;

                    if ($item) {
                        $item->update($payload);
                    } else {
                        Item::create($payload);
                    }
                } catch (\Throwable $e) {
                    $row['row'] = $i + 2; // +2 karena array dimulai dari 0 dan +1 untuk header CSV
                    $row['error'] = $e->getMessage();
                    $failedRows[] = $row;
                }
            }



            // Jika ada row yang gagal, langsung return CSV dari memory
            if (count($failedRows) > 0) {
                DB::rollBack();

                $filename = 'failed_import_' . now()->format('Ymd_His') . '.csv';

                $callback = function () use ($failedRows) {
                    $file = fopen('php://output', 'w');
                    fputcsv($file, array_keys($failedRows[0])); // tulis header

                    foreach ($failedRows as $row) {
                        fputcsv($file, $row);
                    }

                    fclose($file);
                };

                return response()->stream($callback, 500, [
                    "Content-Type"        => "text/csv",
                    "Content-Disposition" => "attachment; filename=\"$filename\"",
                    "Pragma"              => "no-cache",
                    "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                    "Expires"             => "0"
                ]);
            }




            DB::commit();

            if($request_source == 'api'){
                return response()->json([
                    'message' => 'CSV uploaded and processed Successfully!',
                    'success' => true,
                    'data' => []
                ]);
            }

            return redirect()->route('items.index')->with('success', 'CSV uploaded and processed Successfully!');
        } catch (\Throwable $th) {
            DB::rollBack();


            if($request_source == 'api'){
                return response()->json(['message' => $th->getMessage(), 'success' => false, 'data' => []], 500);
            }

            return back()->with('error', 'Failed to import csv. Please try again.' . $th->getMessage());
        }
    }



    public function importTemplate()
    {
        $headers = ['Content-Type' => 'text/csv'];
        $filename = "import_template.csv";

        // Define your column headers (template)
        $columns = ['code', 'sku', 'name', 'price', 'cost', 'weight (gram)', 'notes'];

        // Open a memory "file" for writing CSV data
        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fclose($file);
        };

        return Response::stream($callback, 200, [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ]);
    }


    public function exportData(Request $request)
    {
        $params = json_decode($request->get('params'), true);
        $space_id = get_space_id($request);



        $query = Item::with('inventories')
                    ->where('space_type', 'SPACE')
                    ->where('space_id', $space_id);

        $space = Space::findOrFail($space_id);
        $spaces = $space->spaceAndChildren();



        // Apply search filter
        if (!empty($params['search']['value'])) {
            $search = $params['search']['value'];
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%$search%")
                ->orWhere('sku', 'like', "%$search%")
                ->orWhere('name', 'like', "%$search%")
                ->orWhere('notes', 'like', "%$search%")
                ->orWhere('status', 'like', "%$search%");
            });
        }

        // Apply ordering
        if (!empty($params['order'][0])) {
            $colIdx = $params['order'][0]['column'];
            $dir = $params['order'][0]['dir'];

            // ambil nama kolom dari index
            $column = $params['columns'][$colIdx]['data'] ?? 'id';
            $query->orderBy($column, $dir);
        }

        $query->take(10000);
        $items = $query->get();



        $filename = 'export_items_' . now()->format('Ymd_His') . '.csv';
        $columns = ['id', 'code', 'sku', 'name', 'price', 'cost', 'weight', 'notes', 'status', 'created_at', 'model_type'];

        foreach($spaces as $space){
            $columns[] = 'stok ' . $space->name;
            $columns[] = 'cost ' . $space->name;
        }


        try {
            $callback = function () use ($items, $columns, $spaces) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);

                foreach ($items as $item) {
                    $row = [
                        $item->id,
                        $item->code,
                        $item->sku,
                        $item->name,
                        $item->price,
                        $item->cost,
                        $item->weight,
                        $item->notes,
                        $item->status,
                        $item->created_at,
                        $item->model_type,
                    ];


                    $ivts = $item->inventories;
                    foreach($spaces as $space){
                        $ivt = $ivts->where('space_id', $space->id)->first() ?? new Inventory();
                        $row[] = $ivt?->balance ?? 0;
                        $row[] = $ivt?->cost_per_unit ?? 0;
                    }

                    fputcsv($file, $row);
                }

                fclose($file);
            };
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'success' => false, 'data' => []], 500);
        }

        return response()->stream($callback, 200, [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=\"$filename\"",
        ]);
    }
}
