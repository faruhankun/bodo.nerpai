<?php

namespace App\Http\Controllers\Primary\Transaction;

use App\Http\Controllers\Controller;
use App\Services\Primary\Transaction\JournalSupplyService;
use App\Services\Primary\Basic\EximService;
use Illuminate\Http\Request;

use Yajra\DataTables\Facades\DataTables;

use App\Models\Primary\Transaction;
use App\Models\Primary\Inventory;
use App\Models\Primary\TransactionDetail;
use App\Models\Primary\Item;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class JournalSupplyController extends Controller
{
    protected $journalSupply, $eximService;

    protected $model_types = [
        ['id' => 'PO', 'name' => 'Purchase'],
        ['id' => 'SO', 'name' => 'Sales'],
        ['id' => 'FND', 'name' => 'Opname Found'],
        ['id' => 'LOSS', 'name' => 'Opname Loss'],
        ['id' => 'DMG', 'name' => 'Damage'],
        ['id' => 'RTR', 'name' => 'Return'],
        ['id' => 'MV', 'name' => 'Move'],
        ['id' => 'UNDF', 'name' => 'Undefined'],
    ];

    protected $import_columns = ['date', 'number', 'sender_notes', 'model_type', 'item_sku', 'item_name', 'notes', 'quantity', 'cost_per_unit', 'debit', 'credit', 'tags'];
    protected $export_columns = [
        'id' => 'id', 
        'number' => 'number', 
        'sender_notes' => 'sender_notes', 
        'status' => 'status', 
        'model_type' => 'model_type',
        'item_sku' => 'item_sku',
        'item_name' => 'item_name',
        'quantity' => 'quantity',
        'cost_per_unit' => 'cost_per_unit',
        'debit' => 'debit',
        'credit' => 'credit',
        'notes' => 'notes', 
        'created_at' => 'created_at',
    ];

    public function __construct(JournalSupplyService $journalSupply, EximService $eximService)
    {
        $this->journalSupply = $journalSupply;
        $this->eximService = $eximService;
    }



    public function get_inventories()
    {
        $space_id = session('space_id') ?? null;

        $inventories = Inventory::with('type', 'parent')->where('model_type', 'SUP');

        if ($space_id) {
            $inventories = $inventories->where('space_type', 'SPACE')
                                    ->where('space_id', $space_id);
        }

        $inventories = $inventories->get();
        return $inventories;
    }


    public function index()
    {
        return view('primary.transaction.journal_supplies.index');
    }




    public function store(Request $request)
    {
        $request_source = get_request_source($request);
        $space_id = get_space_id($request);

        try {
            $validated = $request->validate([
                'sender_id' => 'required',
                'sent_time' => 'nullable',
                'sender_notes' => 'nullable|string|max:255',
            ]);

            $data = [
                'space_id' => $space_id,
                'sender_id' => $validated['sender_id'],
                'sent_time' => $validated['sent_time'] ?? now(),
                'sender_notes' => $validated['sender_notes'],
            ];

            $journal = $this->journalSupply->addJournal($data);


            if($request_source == 'api'){
                return response()->json([
                    'data' => array($journal),
                    'success' => true,
                    'message' => "Journal {$journal->id} Created Successfully!",
                ]);
            }


            return redirect()->route('journal_supplies.edit', $journal->id)
                            ->with('success', "Journal {$journal->id} Created Successfully!");
        } catch (\Throwable $th) {
            if($request_source == 'api'){
                return response()->json([
                    'data' => [],
                    'success' => false,
                    'message' => $th->getMessage(),
                ]);
            }

            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }


    public function show(String $id)
    {
        try {
            $journal = Transaction::with(['details', 'details.detail', 'details.detail.item'])->findOrFail($id);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'success' => false], 404);
        }

        return response()->json([
            'data' => array($journal),
            'recordFiltered' => 1,
            'success' => true,
        ]);
    }



    public function edit(String $id)
    {
        $inventories = $this->get_inventories();
        $inventories = Item::with('type', 'parent')
                            ->where('model_type', 'PRD')->get();
        $journal = Transaction::with(['details', 'details.detail'])->findOrFail($id);
        $model_types = $this->model_types;

        return view('primary.transaction.journal_supplies.edit', compact('journal', 'inventories', 'model_types'));
    }



    public function update(String $id, Request $request)
    {
        $request_source = get_request_source($request);


        try {
            $validated = $request->validate([
                'sent_time' => 'nullable',
                'handler_id' => 'required',
                'handler_notes' => 'nullable|string|max:255',
                'details' => 'nullable|array',
                'details.*.detail_id' => 'required',
                'details.*.quantity' => 'required|numeric',
                'details.*.model_type' => 'required|string',
                'details.*.cost_per_unit' => 'required|min:0',
                'details.*.notes' => 'nullable|string|max:255',
            ]);

            if(!isset($validated['details'])){
                $validated['details'] = [];
            }

            $journal = Transaction::with(['details'])->findOrFail($id);

            $data = [
                'sent_time' => $validated['sent_time'] ?? now(),
                'handler_notes' => $validated['handler_notes'] ?? null,
                'handler_type' => 'PLAY',
                'handler_id' => $validated['handler_id'],
            ];

            $this->journalSupply->updateJournal($journal, $data, $validated['details']);



            if($request_source == 'api'){
                return response()->json([
                    'data' => array($journal),
                    'success' => true,
                    'message' => "Journal {$journal->id} updated successfully!",
                ]);
            }

            return redirect()->route('journal_supplies.index')
                ->with('success', "Journal {$journal->id} updated successfully!");
        } catch (\Throwable $th) {
            if($request_source == 'api'){
                return response()->json(['message' => $th->getMessage(), 'success' => false], 404);
            }

            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }



    public function destroy(Request $request, String $id)
    {
        $request_source = get_request_source($request);

        try {
            $journal = Transaction::findOrFail($id);
            $journal->delete();

            $journal->details()->delete();


            if($request_source == 'api'){
                return response()->json([
                    'data' => array($journal),
                    'success' => true,
                    'message' => 'Journal Entry deleted successfully',
                ]);
            }
            return redirect()->route('journal_supplies.index')
                ->with('success', 'Journal Entry deleted successfully');
        } catch (\Throwable $th) {
            if($request_source == 'api'){
                return response()->json(['message' => $th->getMessage(), 'success' => false], 404);
            }
            return back()->with('error', 'Failed to delete journal entry. Please try again.');
        }
    }



    public function getJournalSuppliesData(Request $request)
    {
        $query = $this->getQueryData($request);       
                                  
        if ($request->has('search') && $request->search['value'] || $request->filled('q')) {
            $search = $request->search['value'] ?? $request->q;

            $query = $query->where(function ($q) use ($search) {
                $q->where('transactions.id', 'like', "%{$search}%")
                    ->orWhere('transactions.sent_time', 'like', "%{$search}%")
                    ->orWhere('transactions.number', 'like', "%{$search}%")
                    ->orWhere('transactions.sent_time', 'like', "%{$search}%")
                    ->orWhere('transactions.handler_notes', 'like', "%{$search}%");

                $q->orWhereHas('details', function ($q2) use ($search) {
                    $q2->where('transaction_details.notes', 'like', "%{$search}%")
                        ->orWhere('transaction_details.model_type', 'like', "%{$search}%")
                    ;
                });

                $q->orWhereHas('details.detail', function ($q2) use ($search) {
                    $q2->where('inventories.name', 'like', "%{$search}%")
                        ->orWhere('inventories.sku', 'like', "%{$search}%")
                    ;
                });
            });
        }

        $query = $query->orderBy('transactions.id', 'desc');

        // dd($query->toSql(), $query->getBindings());



        return DataTables::of($query)
            ->addColumn('actions', function ($data) {
                $route = 'journal_supplies';

                $actions = [
                    'show' => 'modal',
                    'show_modal' => 'primary.transaction.journal_supplies.show',
                    'edit' => 'button',
                    'delete' => 'button',
                ];

                return view('components.crud.partials.actions', compact('data', 'route', 'actions'))->render();
            })
            ->addColumn('sku', function ($data){
                return $data->sku_list ?? '';
            })
            ->addColumn('details_first_notes', function ($data){
                return $data->details->first()->notes ?? '';
            })
            ->filter(function ($query) use ($request) {
                
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    
    public function getQueryData(Request $request){
        $space_id = get_space_id($request);

        $query = Transaction::with('input', 'type', 'details', 'details.detail', 'details.detail.item')
            ->where('model_type', 'JS')
            ->orderBy('sent_time', 'desc');
        // $query = Transaction::query()
        //     ->selectRaw('transactions.*, GROUP_CONCAT(items.sku SEPARATOR ", ") as sku_list')
        //     ->join('transaction_details', 'transaction_details.transaction_id', '=', 'transactions.id')
        //     ->join('inventories', function ($join) {
        //         $join->on('inventories.id', '=', 'transaction_details.detail_id')
        //             ->where('transaction_details.detail_type', '=', 'IVT');
        //     })
        //     ->join('items', function ($join) {
        //         $join->on('items.id', '=', 'inventories.item_id')
        //             ->where('inventories.item_type', '=', 'ITM');
        //     })
        //     ->where('transactions.model_type', 'JS') // atau sesuaikan kebutuhanmu
        //     ->groupBy('transactions.id');


        $query = $query->where('transactions.space_type', 'SPACE')
                        ->where('transactions.space_id', $space_id);

        return $query;
    }



    // Export Import
    public function importTemplate(){
        $response = $this->eximService->exportCSV(['filename' => 'journal_supplies_import_template.csv'], $this->import_columns);

        return $response;
    }


    public function exportData(Request $request)
    {
        $request_source = get_request_source($request);
        $params = json_decode($request->get('params'), true);
        

        $query = $this->getQueryData($request);
        // search & order filter
        $query = $this->eximService->exportQuery($query, $params, ['id', 'sent_time', 'number', 'sender_notes', 'total']);


        // Limit
        $limit = $request->get('limit');
        if($limit){
            if($limit != 'all'){
                $query->limit($limit);
            } 
        } else {
            $query->limit(50);
        }


        // $query->take(10000);
        $collects = $query->get();


        // Prepare the CSV data
        $filename = 'export_journal_supplies_' . now()->format('Ymd_His') . '.csv';
        $data = collect();

        // fetch transation into array
        // grouped by number
        foreach($collects as $collect){
            $row = [];

            $row['number'] = $collect->number;
            $row['date'] = $collect->sent_time->format('d/m/Y');
            $row['sender_notes'] = $collect->sender_notes;
            $row['status'] = $collect->status;

            foreach($collect->details as $detail){
                $row['model_type'] = $detail->model_type ?? 'no model type';
                $row['item_sku'] = $detail->detail->sku ?? 'no sku';
                $row['item_name'] = $detail->detail->name ?? 'no name';
                $row['quantity'] = $detail->quantity;
                $row['cost_per_unit'] = $detail->cost_per_unit;
                $row['debit'] = $detail->debit;
                $row['credit'] = $detail->credit;
                $row['notes'] = $detail->notes;
                $row['created_at'] = $collect->created_at;

                $data[] = $row;
            }
        }

        $response = $this->eximService->exportCSV(['filename' => $filename, 'request_source' => $request_source], $data);

        return $response;
    }


    public function importData(Request $request)
    {
        $space_id = get_space_id($request);
        $request_source = get_request_source($request);
        $player_id = $request->player_id ?? (session('player_id') ?? auth()->user()->player->id);

        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'file' => 'required|mimes:csv,txt'
            ]);

            $file = $validated['file'];
            $data = collect();
            $failedRows = collect();
            $requiredHeaders = ['date', 'number', 'model_type', 'item_sku', 'quantity'];

            // Read the CSV into an array of associative rows
            $data = $this->eximService->convertCSVtoArray($file, ['requiredHeaders' => $requiredHeaders]);


            // Group by transaction number
            $data_by_number = collect($data)->groupBy('number');

            // dd($data_by_number);

            foreach($data_by_number as $txnNumber => $rows){
                try {
                    $row_first = $rows[0];

                    // header transaction
                    $header = [
                        'number' => $txnNumber,
                        'space_type' => 'SPACE',
                        'space_id' => $space_id,
                        'model_type' => 'JS',
                        'sender_type' => 'PLAY',
                        'sender_id' => $player_id,
                        'handler_type' => 'PLAY',
                        'handler_id' => $player_id,
                        'sent_time' => empty($row_first['date']) ? Date('Y-m-d') : $row_first['date'],
                        'sender_notes' => $row_first['sender_notes'] ?? null,
                    ];

                    $tx_details = collect();
                    $tx_total = 0;

                    foreach($rows as $i => $row){
                        try {
                            // skip if no code or name
                            if (empty($row['item_sku']) && empty($row['item_name'])) {
                                throw new \Exception('Missing required field: item_sku && item_name');
                            }
    
    
                            // look up item
                            $item = Item::Where('sku', $row['item_sku'])
                                        ->orWhere('name', $row['item_name'])
                                        ->first();
    
                            // create or use item
                            if(!$item){
                                $item = Item::create([
                                    'sku' => $row['item_sku'],
                                    'name' => $row['item_name'],
                                    'price' => $row['item_price'] ?? 0,
                                    'cost' => $row['item_cost'] ?? 0,
                                    'weight' => $row['item_weight (gram)'] ?? 0,
                                    'notes' => $row['notes'] ?? null,
                                ]);
                            }
    
    
                            // check for supply
                            $supply = Inventory::where('model_type', 'SUP')
                                                ->where('item_type', 'ITM')
                                                ->where('space_type', 'SPACE')
                                                ->where('space_id', $space_id);
    
                            
                            // check supply exists
                            $supply = $supply->where('item_id', $item->id)
                                                ->first();
    
    
                            // create supply if not exist
                            if (!$supply) {
                                $supply = Inventory::create([
                                    'space_type' => 'SPACE',
                                    'space_id' => $space_id,
    
                                    'sku' => $item->sku,
                                    'name' => $item->name,
                                    'item_id' => $item->id,
                                    'cost_per_unit' => $item->cost,
                                    
                                    'model_type' => 'SUP',
                                    'item_type' => 'ITM',
                                    'parent_type' => 'IVT',
                                ]);
                            }
    
                            $tx_details->push([
                                'detail_id' => $supply->id,
                                'model_type' => $row['model_type'] ?? 'UNDF',
                                'quantity' => $row['quantity'] ?? 0,
                                'cost_per_unit' => $row['cost_per_unit'] ?? 0,
                                'notes' => $row['notes'] ?? null,
                            ]);
                        } catch (\Throwable $e) {
                            $row['row'] = $i + 1; 
                            $row['error'] = $e->getMessage();
                            $failedRows[] = $row;
                        }
                    }
                    
                    // find tx, create if not exist
                    $tx = Transaction::where('number', $txnNumber)
                                        ->where('model_type', 'JS')
                                        ->where('space_type', 'SPACE')
                                        ->where('space_id', $space_id)
                                        ->first();

                    if (!$tx) {
                        $tx = Transaction::create($header);
                    }

                    // update
                    $this->journalSupply->updateJournal($tx, $header, $tx_details->toArray());
                } catch (\Throwable $e) {
                    DB::rollBack();

                    if($request_source == 'api'){ return response()->json(['message' => $e->getMessage(), 'success' => false, 'data' => []], 500); }

                    return back()->with('error', 'Theres an error on tx number ' . $txnNumber . '. Please try again.' . $e->getMessage());
                }
            }


            // Jika ada row yang gagal, langsung return CSV dari memory
            if (count($failedRows) > 0) {
                DB::rollBack();

                $filename = 'failed_import_' . now()->format('Ymd_His') . '.csv';
                
                $this->eximService->exportCSV(['filename' => $filename, 'request_source' => $request_source], $failedRows);
            }


            DB::commit();
            if($request_source == 'api'){ return response()->json(['message' => 'CSV uploaded and processed Successfully!', 'success' => true, 'data' => []], 200); }
            return redirect()->route('journal_supplies.index')->with('success', 'CSV uploaded and processed Successfully!');
        } catch (\Throwable $th) {

            DB::rollBack();
            if($request_source == 'api'){ return response()->json(['message' => $th->getMessage(), 'success' => false, 'data' => []], 500); }
            return back()->with('error', 'Failed to import csv. Please try again.' . $th->getMessage());
        }
    }
}
