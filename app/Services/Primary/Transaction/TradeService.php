<?php

namespace App\Services\Primary\Transaction;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Facades\DataTables;

use App\Services\Primary\Basic\EximService;
use App\Services\Primary\Basic\SpaceService;

use App\Models\Primary\Player;
use App\Models\Primary\Person;
use App\Models\Primary\Group;
use App\Models\Primary\Relation;
use App\Models\Primary\Transaction;
use App\Models\Primary\Space;
use App\Models\Primary\TransactionDetail;
use App\Models\Primary\Item;


use Carbon\Carbon;



class TradeService
{
    protected $eximService;
    protected $spaceService;
    
    protected $import_columns = [
        'date', 
        'number', 
        'sender_notes',
        
        'receiver_id',
        'receiver_name',
        'receiver_email',
        'receiver_phone', 
        'receiver_address',
        'receiver_notes',

        'model_type', 
        'item_sku', 
        'item_name', 
        'quantity', 
        'price', 
        'discount', 
        'weight', 
        'notes', 
        'tags'
    ];
    

    protected $routerName = 'trades';

    public $summary_types = [
        'geography' => 'Geografi - Lokasi',
        'sales' => 'Sales - Penjualan',
    ];


    public $model_types = [
        ['id' => 'ITR', 'name' => 'Interaksi'],
        ['id' => 'PO', 'name' => 'Purchase'],
        ['id' => 'SO', 'name' => 'Sales'],
        ['id' => 'PRE', 'name' => 'Pre Order'],
        ['id' => 'DMG', 'name' => 'Damage'],
        ['id' => 'RTR', 'name' => 'Return'],
        ['id' => 'MV', 'name' => 'Move'],
        ['id' => 'UNDF', 'name' => 'Undefined'],
    ];


    public $status_types = [
        'TX_DRAFT' => 'DRAFT',
        'TX_REQUEST' => 'REQUEST',
        'TX_APPROVED' => 'APPROVED',
        'TX_CANCELLED' => 'CANCELLED',
        'TX_COMPLETED' => 'COMPLETED',
        'TX_REJECTED' => 'REJECTED',
        'TX_DELETED' => 'DELETED',
        'TX_CLOSED' => 'CLOSED',
        'TX_SHIP' => 'SHIP',
    ];




    public function __construct(EximService $eximService
                                , SpaceService $spaceService)
    {
        $this->eximService = $eximService;
        $this->spaceService = $spaceService;
    }



    // crud
    public function addJournal($data, Request $request, $details = [])
    {
        $player_id = $data['sender_id'] ?? get_player_id($request, false);
        $space_id = $data['space_id'] ?? (get_space_id($request) ?? null);

        $tx = Transaction::create([
            'space_type' => 'SPACE',
            'space_id' => $data['space_id'] ?? $space_id,
            'model_type' => 'TRD',
            'sender_type' => $data['sender_type'] ?? 'PLAY',
            'sender_id' => $data['sender_id'] ?? $player_id,
            'input_type' => $data['input_type'] ?? null,
            'input_id' => $data['input_id'] ?? null,
            'sent_time' => $data['sent_time'] ?? Date('Y-m-d'),
            'sender_notes' => $data['sender_notes'] ?? null,
            'total' => $data['total'] ?? 0,
        ]);

        $tx->generateNumber();
        $tx->save();

        return $tx;
    }


    public function updateJournal($tx, $data, $details = [])
    {
        // Update main journal entry
        $tx->update($data);

        // Delete old details
        $tx->details()->delete();

        // Create new details
        $journalDetails = [];
        $balance_change = 0;
        foreach ($details as $detail) {
            // inventory_id
            $detail['quantity'] = $detail['quantity'] ?? 0;

            $detail['detail_id'] = $detail['detail_id'] ?? null;
            $detail['debit'] = $detail['quantity'] >= 0 ? $detail['quantity'] : 0;
            $detail['credit'] = $detail['quantity'] < 0 ? abs($detail['quantity']) : 0;
            $detail['notes'] = $detail['notes'] ?? null;
            $detail['model_type'] = $detail['model_type'] ?? 'UNDF';
            $detail['price'] = $detail['price'] ?? 0;
            $detail['discount'] = $detail['discount'] ?? 0;

            $detail['sku'] = $detail['sku'] ?? null;
            $detail['name'] = $detail['name'] ?? null;
            $detail['weight'] = $detail['weight'] ?? 0;

            $journalDetails[] = [
                'transaction_id' => $tx->id,
                'detail_type' => 'ITM',
                'detail_id' => $detail['detail_id'],
                'debit' => $detail['debit'],
                'credit' => $detail['credit'],
                'notes' => $detail['notes'] ?? null,
                'quantity' => $detail['quantity'],
                'model_type' => $detail['model_type'],
                'price' => $detail['price'],
                'discount' => $detail['discount'],
                'sku' => $detail['sku'],
                'name' => $detail['name'],
                'weight' => $detail['weight'],
            ];

            $balance_change += $detail['quantity'] * $detail['price'] * (1 - $detail['discount']);
        }

        $tx->details()->createMany($journalDetails);

        if(is_null($tx->number))
            $tx->generateNumber();
        $tx->total = $balance_change;
        $tx->save();

        return $tx;
    }



    public function getData(Request $request){
        $space_id = get_space_id($request);

        $query = $this->getQueryData($request);



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
                $q->where('id', 'like', "%{$keyword}%")
                ->orWhere('number', 'like', "%{$keyword}%")

                ->orWhere('sent_time', 'like', "%{$keyword}%")
                ->orWhere('received_time', 'like', "%{$keyword}%")
                
                ->orWhere('status', 'like', "%{$keyword}%")

                ->orWhere('sender_notes', 'like', "%{$keyword}%")
                ->orWhere('receiver_notes', 'like', "%{$keyword}%")
                ->orWhere('handler_notes', 'like', "%{$keyword}%");
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



        $return_type = $request->get('return_type') ?? 'json';
        if($return_type == 'DT'){
            return DataTables::of($query)
                ->addColumn('actions', function ($data) {
                    $route = 'trades';

                    $actions = [
                        'show' => 'modaljs',
                        'edit' => 'button',
                        'delete' => 'button',
                    ];


                    // jika punya input atau children, maka tidak bisa dihapus
                    if($data->outputs->isNotEmpty() || $data->input){
                        unset($actions['delete']);
                    }


                    return view('components.crud.partials.actions', compact('data', 'route', 'actions'))->render();
                })

                ->addColumn('sku', function ($data){
                    return $data->details->map(function ($detail){
                        return $detail->detail->sku;
                    })->implode(', ');
                })

                ->addColumn('all_notes', function ($data){
                    return $data->sender_notes . '<br>' . $data->handler_notes;
                })

                ->addColumn('data', function ($data) {
                    return $data;
                })

                ->filter(function ($query) use ($request) {                                  
                    if ($request->has('search') && $request->search['value'] || $request->filled('q')) {
                        $search = $request->search['value'] ?? $request->q;

                        $query = $query->where(function ($q) use ($search) {
                            $q->where('transactions.id', 'like', "%{$search}%")
                                ->orWhere('transactions.sent_time', 'like', "%{$search}%")
                                ->orWhere('transactions.number', 'like', "%{$search}%")
                                ->orWhere('transactions.sender_notes', 'like', "%{$search}%")
                                ->orWhere('transactions.handler_notes', 'like', "%{$search}%");

                            $q->orWhereHas('details', function ($q2) use ($search) {
                                $q2->where('transaction_details.notes', 'like', "%{$search}%")
                                    ->orWhere('transaction_details.model_type', 'like', "%{$search}%")
                                ;
                            });

                            $q->orWhereHas('details.detail', function ($q2) use ($search) {
                                $q2->where('name', 'like', "%{$search}%")
                                    ->orWhere('sku', "{$search}")
                                ;
                            });
                        });
                    }    
                })

                ->rawColumns(['actions', 'all_notes'])
                ->make(true);
            }



            // return result
            return DataTables::of($query)->make(true);
    } 



    // index
    public function getQueryData(Request $request){
        $space_id = get_space_id($request);

        $query = Transaction::with('input', 'type', 'details', 'details.detail', 
                                    'sender', 'handler', 'receiver')
                            ->where('model_type', 'TRD')
                            ->where('space_type', 'SPACE')
                            ->where('space_id', $space_id);


        // filter model
        $model_type_select = $request->get('model_type_select') ?? 'null';
        if($model_type_select != 'all'){
            if($model_type_select == 'null' || empty($model_type_select)){
                $query->whereDoesntHave('details');
            } else {
                $query->whereHas('details', function($q) use ($model_type_select){
                    $q->where('model_type', $model_type_select);
                });
            }
        }


        return $query;
    }



    public function getIndexData(Request $request){
        $trades = $this->getQueryData($request);

        return DataTables::of($trades)
            ->addColumn('size_display', function ($data) {
                return ($data->size_type ?? '?') . ' : ' . ($data->size?->number ?? '?');
            })
            ->addColumn('actions', function ($data) {
                $route = 'trades';
                
                $actions = [
                    'show' => 'modaljs',
                    // 'show_modal' => 'space.trades.show',
                    // 'edit' => 'modal',
                    'delete' => 'button',
                ];

                return view('components.crud.partials.actions', compact('data', 'route', 'actions'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }



    // Summary
    public function getSummaryData(Request $request){
        $query = $request->get('summary_type');

        try {
            switch($query){
                case 'geography':
                    $response = $this->getSummaryDataGeography($request);
                    break;
                case 'sales':
                    $response = $this->getSummaryDataSales($request);
                    break;
                default:
                    $response = response()->json(['error' => 'Invalid query'], 400);
                    break;

            }
            
            return $response;
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function getSummaryDataSales(Request $request){
        return null;
    }

    public function getSummaryDataGeography(Request $request){
        $space_id = $this->getSpaceId($request);

        $page = $request->get('page', 1);
        $per_page = $request->get('per_page', 50);
        $offset = ($page - 1) * $per_page;

        $query = Player::with('size', 'type')
                        ->whereIn('players.id', function ($query) use ($space_id) {
                            $query->select('model2_id')
                                ->from('relations')
                                ->where('model2_type', 'PLAY')
                                ->where('model1_type', 'SPACE')
                                ->where('model1_id', $space_id);
                        })
                        ->join('persons', 'persons.id', '=', 'players.size_id');

        // search & order
        if($request->has('address')){
            if($request->filled('address')){
                $query = $query->where('persons.address', 'like', "%{$request->address}%");
            } else {
                $query = $query->orWhere(function($q) {
                    $q->whereNull('persons.address')
                    ->orWhere('persons.address', '');
                });
            }
        }

        if($request->exists('province')){
            if($request->filled('province')){
                $query = $query->where('persons.province', $request->province);
            } else {
                $query = $query->where(function($q) {
                    $q->whereNull('persons.province')
                    ->orWhere('persons.province', '');
                });
            }
        }

        if($request->has('regency')){
            if($request->filled('regency')){
                $query = $query->where('persons.regency', $request->regency);
            } else {
                $query = $query->where(function($q) {
                    $q->whereNull('persons.regency')
                    ->orWhere('persons.regency', '');
                });
            }
        }

        $trades = (clone $query)->skip($offset)->take($per_page)->get();


        $response = [
            'page' => $page,
            'per_page' => $per_page,
            'total' => $query->count(),
            'data' => $trades,
            'request' => $request->all(),
        ];

        if($request->filled('byRegency')){
            $byRegency = (clone $query)
                            ->select('persons.regency', DB::raw('count(*) as total'))
                            ->groupBy('persons.regency')
                            ->orderBy('total', 'desc')
                            ->get();
            $response['byRegency'] = $byRegency;
            $response['province'] = $request->province;
        }
        if($request->filled('byProvince')){
            $byProvince = (clone $query)
                            ->select('persons.province', DB::raw('count(*) as total'))
                            ->groupBy('persons.province')
                            ->orderBy('total', 'desc')
                            ->get();
            $response['byProvince'] = $byProvince;
        }


        return response()->json($response);
    }




    // Export Import
    public function getImportTemplate(){
        $response = $this->eximService->exportCSV(['filename' => "{$this->routerName}_import_template.csv"], $this->import_columns);

        return $response;
    }


    public function exportData(Request $request)
    {
        $params = json_decode($request->get('params'), true);
        
        $query = $this->getQueryData($request);
        // search & order filter
        $query = $this->eximService->exportQuery($query, $params, ['number', 'sender_notes', 'sent_date', 'id']);

        

        // Limit
        $limit = $request->get('limit');
        if($limit){
            if($limit != 'all'){
                $query->limit($limit);
            } 
        } else {
            $query->limit(1000);
        }
        $collects = $query->get();


        // Prepare the CSV data
        $filename = "export_{$this->routerName}_" . now()->format('Ymd_His') . '.csv';
        $data = collect();

        // fetch transation into array
        // grouped by number
        foreach($collects as $collect){
            $row = [];

            $row['number'] = $collect->number;
            $row['date'] = $collect->sent_time->format('Y-m-d');
            $row['sender_notes'] = $collect->sender_notes;
            $row['status'] = $collect->status;


            // receiver
            if($collect->receiver){
                $row['receiver_type'] = $collect->receiver_type;
                $row['receiver_id'] = $collect->receiver_id;
                $row['receiver_name'] = $collect->receiver->name ?? 'no name';
                $row['receiver_address'] = $collect->receiver->address ?? 'no address';
                $row['receiver_notes'] = $collect->receiver_notes;
            }


            foreach($collect->details as $detail){
                $row['model_type'] = $detail->model_type ?? 'no model type';
                $row['item_sku'] = $detail->sku ?? 'no sku';
                $row['item_name'] = $detail->name ?? 'no name';
                $row['quantity'] = $detail->quantity;
                $row['price'] = $detail->price;
                $row['discount'] = $detail->discount;
                $row['weight'] = $detail->weight;
                $row['notes'] = $detail->notes;
                $row['created_at'] = $collect->created_at;

                $data[] = $row;
            }
        }


        $response = $this->eximService->exportCSV(['filename' => $filename], $data);

        return $response;
    }


    public function importData(Request $request)
    {
        $space_id = get_space_id($request);

        $space = Space::findOrFail($space_id);
        $spaces = array($space_id);
        $space_parent_id = $space->parent_id ?? null;

        if($space->parent_id){
            $spaces[] = $space->parent_id;
        }


        $request_source = get_request_source($request);
        $player_id = get_player_id($request);



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
                        'model_type' => 'TRD',
                        'sender_type' => 'PLAY',
                        'sender_id' => $player_id,
                        'handler_type' => 'PLAY',
                        'handler_id' => $player_id,
                        'sent_time' => empty($row_first['date']) ? Date('Y-m-d') : $row_first['date'],
                        'sender_notes' => $row_first['sender_notes'] ?? null,

                        'receiver_type' => 'PLAY',
                        'receiver_id' => $row_first['receiver_id'] ?? null,
                        'receiver_notes' => $row_first['receiver_notes'] ?? null
                    ];


                    $tx_details = collect();
                    $tx_total = 0;



                    $receiver = Player::query();
                    if(isset($row_first['receiver_id']) && !empty($row_first['receiver_id'])){
                        $receiver = $receiver->where('id', $row_first['receiver_id']);
                    } else {
                        if(isset($row_first['receiver_name']) && !empty($row_first['receiver_name']))
                            $receiver = $receiver->where('name', 'like', '%' . $row_first['receiver_name'] . '%');

                        if(isset($row_first['receiver_email']) && !empty($row_first['receiver_email']))
                            $receiver = $receiver->where('email', 'like', '%' . $row_first['receiver_email'] . '%');

                        if(isset($row_first['receiver_phone']) && !empty($row_first['receiver_phone']))
                            $receiver = $receiver->where('phone', 'like', '%' . $row_first['receiver_phone'] . '%');
                    }
                    $receiver = $receiver->first();

                    if(!$receiver && isset($row_first['receiver_name']) && !empty($row_first['receiver_name'])){
                        $receiver = Player::create([
                            'name' => $row_first['receiver_name'] ?? 'no name',
                            'email' => $row_first['receiver_email'] ?? null,
                            'phone' => $row_first['receiver_phone'] ?? null,
                            'address' => $row_first['receiver_address'] ?? null,
                            'notes' => $row_first['receiver_notes'] ?? null,
                            'space_type' => 'SPACE',
                            'space_id' => $space_id,
                        ]);
                    }

                    $header['receiver_id'] = $receiver->id ?? null;


                    foreach($rows as $i => $row){
                        try {
                            // skip if no code or name
                            if (empty($row['item_sku']) && empty($row['item_name'])) {
                                throw new \Exception('Missing required field: item_sku && item_name');
                            }
    
    
                            // look up item
                            $item = Item::whereIn('space_id', $spaces)
                                        ->where('space_type', 'SPACE')
                                        ->where(function ($q) use ($row) {
                                            $q->where('sku', $row['item_sku'])
                                            ->orWhere('name', $row['item_name']);
                                        })
                                        ->first();
    

                            
                            // create or use item
                            if(!$item){
                                $item = Item::create([
                                    'sku' => $row['item_sku'],
                                    'name' => $row['item_name'],
                                    'price' => $row['price'] ?? 0,
                                    'cost' => $row['cost'] ?? 0,
                                    'weight' => $row['weight'] ?? 0,
                                    'notes' => $row['notes'] ?? null,
                                    'space_type' => 'SPACE',
                                    'space_id' => $space_parent_id ?? $space_id,
                                ]);
                            }
    
    
                            $tx_details->push([
                                'detail_id' => $item->id,
                                'model_type' => $row['model_type'] ?? 'UNDF',
                                'quantity' => $row['quantity'] ?? 0,
                                'price' => $row['price'] ?? 0,
                                'discount' => $row['discount'] ?? 0,
                                'weight' => $row['weight'] ?? 0,
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
                                        ->where('model_type', 'TRD')
                                        ->where('space_type', 'SPACE')
                                        ->where('space_id', $space_id)
                                        ->first();

                    if (!$tx) {
                        $tx = Transaction::create($header);
                    }


                    // update
                    $this->updateJournal($tx, $header, $tx_details->toArray());
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
                
                return $this->eximService->exportCSV(['filename' => $filename, 'request_source' => $request_source], $failedRows);
            }
        } catch (\Throwable $th) {

            DB::rollBack();
            if($request_source == 'api'){ return response()->json(['message' => $th->getMessage(), 'success' => false, 'data' => []], 500); }
            return back()->with('error', 'Failed to import csv. Please try again.' . $th->getMessage());
        }

        
        DB::commit();
        if($request_source == 'api'){ return response()->json(['message' => 'CSV uploaded and processed Successfully!', 'success' => true, 'data' => []], 200); }
        return redirect()->route('trades.index')->with('success', 'CSV uploaded and processed Successfully!');
    }
}
