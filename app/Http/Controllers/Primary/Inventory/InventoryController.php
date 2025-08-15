<?php

namespace App\Http\Controllers\Primary\Inventory;

use App\Http\Controllers\Controller;
use App\Services\Primary\Basic\EximService;

use Yajra\DataTables\Facades\DataTables;

use App\Models\Primary\Inventory;
use App\Models\Primary\Space;
use App\Models\Primary\Item;
use App\Models\Primary\Transaction;
use App\Models\Primary\TransactionDetail;


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



    // testing react
    public function getSupplyTransactions(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'nullable|integer',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'search' => 'nullable|string',
            'page' => 'nullable|integer',
            'per_page' => 'nullable|integer',
            'model_type' => 'nullable|string',
        ]);

        $validated['start_date'] = $validated['start_date'] ?? now()->startOfYear()->format('Y-m-d');
        $validated['end_date'] = $validated['end_date'] ?? now()->format('Y-m-d');
        $validated['account_id'] = $validated['account_id'] ?? null;
        $validated['model_type'] = $validated['model_type'] ?? null;


        $page = $validated['page'] ?? 1;
        $perPage = $validated['per_page'] ?? 10;
        $offset = ($page - 1) * $perPage;
        $search = $validated['search'] ?? null;


        $baseQuery = TransactionDetail::with(['transaction', 'detail'])
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->select('transaction_details.*')
            ->orderBy('transactions.sent_time', 'asc')
            ->where('transactions.model_type', 'JS')
            ;
            // ->whereBetween('sent_time', [
            //     Carbon::parse($validated['start_date'])->startOfDay(),
            //     Carbon::parse($validated['end_date'])->endOfDay()
            // ])

        if(!is_null($validated['account_id']) && $validated['account_id'] != '') 
            $baseQuery->where('detail_id', $validated['account_id']);
        

        if(!is_null($validated['model_type']) && $validated['model_type'] != '')
            $baseQuery->where('transaction_details.model_type', $validated['model_type']);


        if ($search) {
            $baseQuery->whereHas('transaction', function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                ->orWhere('sender_notes', 'like', "%{$search}%");
            });
        }



        $initQuery = clone $baseQuery;
        $initBalance = 0;

        if(!is_null($validated['start_date']) && $validated['start_date'] != ''){
            $baseQuery->where('transactions.sent_time', '>=', Carbon::parse($validated['start_date'])->startOfDay());

            $initQuery->where('transactions.sent_time', '<', Carbon::parse($validated['start_date'])->startOfDay());
            $initTR = $initQuery->get();
            $initBalance += $initTR->sum(function ($item) {
                return floatval($item->debit ?? 0) - floatval($item->credit ?? 0);
            });
        }

        if(!is_null($validated['end_date']) && $validated['end_date'] != ''){
            $baseQuery->where('transactions.sent_time', '<=', Carbon::parse($validated['end_date'])->endOfDay());
        }



        // Total
        $total = (clone $baseQuery)->count();

        // Get full data before current page for initial balance
        $initials = (clone $baseQuery)
            ->skip(0)
            ->take($offset)
            ->get();

        $initialDebit = $initials->sum(function ($item) {
            return floatval($item->debit ?? 0);
        });
        $initialCredit = $initials->sum(function ($item) {
            return floatval($item->credit ?? 0);
        });
        $initialBalance = $initialDebit - $initialCredit + $initBalance;



        // Current page data
        $results = (clone $baseQuery)
            ->skip($offset)
            ->take($perPage)
            ->get();

        $pageDebit = $results->sum(function ($item) {
            return floatval($item->debit ?? 0);
        });
        $pageCredit = $results->sum(function ($item) {
            return floatval($item->credit ?? 0);
        });

        $finalBalance = $initialBalance + $pageDebit - $pageCredit;



        return response()->json([
            'total' => $total,
            'initial_balance' => $initialBalance,
            'final_balance' => $finalBalance,
            'initial_debit' => $initialDebit,
            'initial_credit' => $initialCredit,
            'page_debit' => $pageDebit,
            'page_credit' => $pageCredit,
            'page' => $page,
            'per_page' => $perPage,
            'data' => $results,
            'input' => $validated
        ]);
    }



    public function getData(Request $request){
        $space_id = get_space_id($request);

        $query = Inventory::with('type', 'item', 'tx_details')
                            ->where('model_type', 'SUP')
                            ->where('space_type', 'SPACE')
                            ->where('space_id', $space_id);


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
                ->orWhere('code', 'like', "%{$keyword}%")
                ->orWhere('id', 'like', "%{$keyword}%")
                ->orWhere('sku', 'like', "%{$keyword}%")
                ->orWhere('notes', 'like', "%{$keyword}%");
            });
        }



        // order by id desc by default
        $orderby = $request->get('orderby');
        $orderdir = $request->get('orderdir');
        if($orderby && $orderdir){
            $query->orderBy($orderby, $orderdir);
        } else {
            $query->orderBy('id', 'asc');
        }



        // return result
        return DataTables::of($query)->make(true);
    } 


    public function index(Request $request)
    {
        $space_id = get_space_id($request);

        return view('primary.inventory.supplies.index');
    }



    public function store(Request $request)
    {
        $request_source = get_request_source($request);
        $space_id = get_space_id($request);

        try {
            $validated = $request->validate([
                'item_id' => 'required',
                'status' => 'nullable|string|max:50',
                'notes' => 'nullable',
            ]);

            $validated['space_type'] = 'SPACE';
            $validated['space_id'] = $space_id;

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

            $ivt = Inventory::updateOrCreate(
                [
                    'sku' => $item->sku,
                    'name' => $item->name,
                    'cost_per_unit' => $item->cost
                ]
                , $validated);


            if($request_source == 'api'){
                return response()->json([
                    'data' => array($ivt),
                    'success' => true,
                    'message' => 'Supply created successfully',
                ]);
            }

            return redirect()->route('supplies.index')->with('success', "Supply {$ivt->name} created successfully.");
        } catch (\Exception $e) {
            if($request_source == 'api'){
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ]);
            }

            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }



    public function update(Request $request, $id){
        try {
            $validated = $request->validate([
                // 'item_id' => 'required',
                'status' => 'nullable|string|max:50',
                'notes' => 'nullable',
            ]);

            $ivt = Inventory::findOrFail($id);
            $ivt->update($validated);


            // update supply balance
            $ivt->updateSupplyBalance();



            return response()->json([
                'data' => array($ivt),
                'success' => true,
                'message' => 'Supply updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'data' => array($ivt),
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }



    public function getSuppliesData(Request $request){
        $space_id = get_space_id($request);

        $supplies = Inventory::with('type', 'item', 'tx_details')
                            ->where('model_type', 'SUP');

        if($space_id){
            $space = Space::findOrFail($space_id);

            $spaceIds = $space->AllChildren()->pluck('id')->toArray();
            $spaceIds = array_merge($spaceIds, [$space_id]);

            $supplies = $supplies->where('space_type', 'SPACE')
                                    ->whereIn('space_id', $spaceIds);
        } 


        $supplies = $supplies->orderBy('id', 'asc');


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

            ->addColumn('cost_total', function ($data) {
                return $data->balance * $data->cost_per_unit;
            })

            ->addColumn('actions', function ($data) {
                $route = 'supplies';
                
                $actions = [
                    // 'show' => 'modal',
                    // 'show_modal' => 'primary.inventory.supplies.show',
                    'edit' => 'modal',
                ];

                return view('components.crud.partials.actions', compact('data', 'route', 'actions'))->render();
            })

            ->addColumn('data', function ($data) {
                return $data;
            })

            ->filter(function ($query) use ($request) {
                if ($request->has('search') && $request->search['value'] || $request->filled('q')) {
                    $search = $request->search['value'] ?? $request->q;

                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%")
                            ->orWhere('sku', "{$search}")
                            ->orWhere('notes', 'like', "%{$search}%")
                            ->orWhere('id', 'like', "%{$search}%");

                        $q->orWhereHas('item', function ($q2) use ($search) {
                            $q2->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%")
                                ->orWhere('sku', "%{$search}%");
                        });
                    });
                }
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
            ->orderBy('id', 'asc')
            ->limit(50) // limit hasil
            ->get()
            ->map(function ($ivt) {
                return [
                    'id' => $ivt->id,
                    'text' => "{$ivt->sku}: {$ivt->name} -qty: {$ivt->balance} x {$ivt->cost_per_unit} : {$ivt->notes}",
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
        $space_id = get_space_id($request);

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
        return response()->json([
            'data' => [],
            'success' => true,
            'message' => 'Under Construction',
        ], 200);
    }




    
    // Summaries
    public $summary_types = [
        'stockflow' => 'Arus Stock',
        'stockflow_items' => 'Arus Stok per Item',
        'balance_stock' => 'Neraca Stock',
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
                            ->where('model_type', 'JS')
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
        $data->items_list = Item::all()->keyBy('id');
        $data = $this->getSummaryData($data, $txs, $spaces, $validated);



        if($request_source == 'api'){
            $data_summary = [];
            if(isset($validated['summary_type']) && isset($data->{$validated['summary_type']})){
                $data_summary = $data->{$validated['summary_type']};
            }

            $spaces_data = $spaces->toArray();
            if($validated['summary_type'] == 'balance_stock'){
                foreach($spaces_data as $key => $space){
                    $spaces_data[$key]['inventory_value'] = isset($data_summary[$space['id']]) && $data_summary[$space['id']] ? $data_summary[$space['id']]->sum('change') : 0;
                }

                $data_summary = $spaces_data;
            }


            // stockflow
            if($validated['summary_type'] == 'stockflow'){
                $stockflow = $this->getSummaryStockflow($txs);
                $data_summary = $stockflow->toArray();
            }


            return response()->json([
                'data' => $data_summary,
                'summary_types' => $this->summary_types,
                'success' => true,
                'spaces' => $spaces_data,
                'input' => $validated,
            ]);
        }

        return view('primary.inventory.supplies.summary', compact('data', 'txs', 'spaces'));
    }

    

    public function getSummaryStockflow($txs){
        $stockflow = collect();

        $txs_per_date = $txs->groupBy('sent_time');

        $space_supply = 0;

        foreach($txs_per_date as $end_date => $txs){
            $per_date_change = [
                'PO' => 0,
                'SO' => 0,
                'FND' => 0,
                'LOSS' => 0,
                'RTR' => 0,
                'DMG' => 0,
                'MV' => 0,
                'UNDF' => 0,
            ];
            
            $per_date = [
                'date' => $end_date,
                'change' => 0,
                'balance' => $space_supply,
            ];

            foreach($txs as $tx){
                foreach($tx->details as $detail){
                    // tx
                    $per_date_change[$detail->model_type] += $detail->quantity * $detail->cost_per_unit;
                }
            }

            $per_date['change'] += array_sum($per_date_change);
            $per_date['balance'] += $per_date['change'];
            $space_supply = $per_date['balance'];

            $per_date = array_merge($per_date, $per_date_change);
            $stockflow->push($per_date);
        }

        return $stockflow;
    }


    public function getSummaryData($data, $txs, $spaces, $validated){
        $summary_type = $validated['summary_type'] ?? null;
        if(is_null($summary_type)){
            return $data;
        }

        // Transaction;
        $spaces_per_id = $spaces->groupBy('id');
        $txs_per_space = $txs->groupBy('space_id');
        $spaces_data = collect();                           
        $items_data = collect();

        foreach($txs_per_space as $id => $txs){
            $txs_per_date = $txs->groupBy('sent_time');

            $space_supply = 0;

            $space_supply_per_date = collect();
            $items_per_space = collect();
            $items = [];

            foreach($txs_per_date as $end_date => $txs){
                $per_date_change = [
                    'PO' => 0,
                    'SO' => 0,
                    'FND' => 0,
                    'LOSS' => 0,
                    'RTR' => 0,
                    'DMG' => 0,
                    'MV' => 0,
                    'UNDF' => 0,
                ];
                
                $per_date = [
                    'change' => 0,
                    'balance' => $space_supply,
                ];

                foreach($txs as $tx){
                    foreach($tx->details as $detail){
                        // tx
                        $per_date_change[$detail->model_type] += $detail->quantity * $detail->cost_per_unit;

                        // item
                        $item = $data->items_list[$detail->detail->item_id] ?? null;
                        if(!isset($items[$item->id])){
                            $items[$item->id] = [
                                'item' => $item, 
                                'in' => 0, 
                                'out' => 0,
                                'in_subtotal' => 0,
                                'out_subtotal' => 0,
                                'omzet' => 0,
                                'margin' => 0,
                            ];
                        }

                        $items[$item->id]['in'] += $detail->debit;
                        $items[$item->id]['out'] += $detail->credit;
                        $items[$item->id]['in_subtotal'] += $detail->debit * $detail->cost_per_unit;
                        $items[$item->id]['out_subtotal'] += $detail->credit * $detail->cost_per_unit;
                        $items[$item->id]['omzet'] += $detail->credit * $item->price;
                        $items[$item->id]['margin'] += $items[$item->id]['omzet'] - $items[$item->id]['out_subtotal'];
                    }
                }

                $per_date['change'] += array_sum($per_date_change);
                $per_date['balance'] += $per_date['change'];
                $space_supply = $per_date['balance'];

                $per_date = array_merge($per_date, $per_date_change);
                $space_supply_per_date->put($end_date, $per_date);
            }

            $spaces_data->put($id, $space_supply_per_date);
            $items_data->put($id, $items);
        }

        $data->spaces_data = $spaces_data;
        $data->stockflow = $spaces_data;
    
        $data->items_data = $items_data;
        $data->stockflow_items = $items_data;

        $data->balance_stock = $spaces_data;

        return $data;
    }
}
