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

use Carbon\Carbon;



class TradeService
{
    protected $eximService;
    protected $spaceService;
    protected $import_columns = [
        'sender_id',
        'sender_code',
        'sender_name',
        
        'sent_date', 
        'sender_notes',
        'input_id',

        'receiver_id',
        'receiver_code',
        'receiver_name',

        'received_date',
        'receiver_notes',
        'output_id',
    ];

    protected $routerName = 'trades';

    public $summary_types = [
        'geography' => 'Geografi - Lokasi',
        'sales' => 'Sales - Penjualan',
    ];


    public $model_types = [
        ['id' => 'PRE', 'name' => 'Pre Order'],
        ['id' => 'SO', 'name' => 'Sales'],
        ['id' => 'DMG', 'name' => 'Damage'],
        ['id' => 'RTR', 'name' => 'Return'],
        ['id' => 'MV', 'name' => 'Move'],
        ['id' => 'UNDF', 'name' => 'Undefined'],
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
                        'show' => 'button',
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
                                $q2->where('inventories.name', 'like', "%{$search}%")
                                    ->orWhere('inventories.sku', "{$search}")
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
                                    'sender', 'receiver')
                            ->where('model_type', 'TRD')
                            ->where('space_type', 'SPACE')
                            ->where('space_id', $space_id);

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

            $row['id'] = $collect->id;
            $row['number'] = $collect->number;

            $row['sender_id'] = $collect->sender_id;
            $row['sender_code'] = $collect->sender?->code;
            $row['sender_name'] = $collect->sender?->name;

            $row['sent_date'] = Carbon::parse($collect->sent_time)->format('Y-m-d'); 
            $row['sender_notes'] = $collect->sender_notes;
            $row['input_id'] = $collect->input_id;

            $row['receiver_id'] = $collect->receiver_id;
            $row['receiver_code'] = $collect->receiver?->code;
            $row['receiver_name'] = $collect->receiver?->name;

            $row['received_date'] = Carbon::parse($collect->received_time)->format('Y-m-d');
            $row['receiver_notes'] = $collect->receiver_notes;
            $row['output_id'] = $collect->output_id;

            $data[] = $row;
        }

        $response = $this->eximService->exportCSV(['filename' => $filename], $data);

        return $response;
    }


    public function importData(Request $request)
    {
        $space_id = $this->getSpaceId($request);
        $player_id = $request->player_id ?? (session('player_id') ?? auth()->user()->player->id);

        try {
            $validated = $request->validate([
                'file' => 'required|mimes:csv,txt'
            ]);


            $file = $validated['file'];
            $data = collect();
            $failedRows = collect();
            $requiredHeaders = ['size_type (*) (PERS/GRP)', 'name (*)'];


            // Read the CSV into an array of associative rows
            $data = $this->eximService->convertCSVtoArray($file, ['requiredHeaders' => $requiredHeaders]);

            
            // process data
            foreach($data as $i => $row){
                try {
                    // skip if no code or name
                    if (empty($row['name (*)'])) {
                        throw new \Exception('Missing required field: name');
                    }


                    $player_data = [
                        'code' => $row['code'] && !empty($row['code']) ? $row['code'] : null,
                        'size_type' => $row['size_type (*) (PERS/GRP)'] && !empty($row['size_type (*) (PERS/GRP)']) ? $row['size_type (*) (PERS/GRP)'] : 'PERS',
                        'size_id' => $row['size_id (id)'] && !empty($row['size_id (id)']) ? $row['size_id (id)'] : null,
                        'name' => $row['name (*)'] && !empty($row['name (*)']) ? $row['name (*)'] : null,
                        'address' => $row['address (json)'] ?? '',
                        'notes' => $row['notes'] ?? null,
                    ];

                    $birthDate = $row['birth_date'] ?? null;
                    $deathDate = $row['death_date'] ?? null;
                    $size_data = [
                        'name' => $row['name (*)'] && !empty($row['name (*)']) ? $row['name (*)'] : null,
                        'full_name' => $row['full_name'] ?? null,
                        'email' => $row['email (*)'] && !empty($row['email (*)']) ? $row['email (*)'] : null,
                        'phone_number' => $row['phone_number (*)'] && !empty($row['phone_number (*)']) ? $row['phone_number (*)'] : null,
                        'address' => $row['address (json)'] ?? '',
                        'birth_date' => (!empty($birthDate) && strtotime($birthDate)) ? date('Y-m-d', strtotime($birthDate)) : null,
                        'death_date' => (!empty($deathDate) && strtotime($deathDate)) ? date('Y-m-d', strtotime($deathDate)) : null,
                        'gender' => $row['gender'] ?? null,
                        'id_card_number' => $row['id_card_number'] ?? null,
                        'notes' => $row['notes'] ?? null,

                        'country' => $row['country'] ?? 'Indonesia',
                        'province' => $row['province'] ?? null,
                        'regency' => $row['regency'] ?? null,
                        'district' => $row['district'] ?? null,
                        'village' => $row['village'] ?? null,
                        'postal_code' => $row['postal_code'] ?? null,
                        'address_detail' => $row['address_detail'] ?? null,
                    ];



                    // look for size
                    if($player_data['size_type'] == 'PERS'){
                        $size = Person::where('name', $size_data['name']);
                    } else if($player_data['size_type'] == 'GRP'){
                        $size = Group::where('name', $size_data['name']);
                    }
                    
                    if(!empty($player_data['size_id'])){
                        $size->where('id', $player_data['size_id']);
                    }

                    if(!empty($player_data['email'])){
                        $size->where('email', $player_data['email']);
                    }

                    if(!empty($player_data['phone_number'])){
                        $size->where('phone_number', $player_data['phone_number']);
                    }

                    $size = $size->first();

                    if($size){
                        // update size
                        $size->update($size_data);
                    } else {
                        // create size
                        if($player_data['size_type'] == 'PERS'){
                            $size = Person::create($size_data);
                        } else if($player_data['size_type'] == 'GRP'){
                            $size = Group::create($size_data);
                        }
                    }



                    // look for player
                    $player = Player::with('type', 'size')
                                    ->where('size_id', $size->id)
                                    ->where('size_type', $player_data['size_type'])
                                    ->where('name', $size_data['name'])
                                    ->first();

                    $player_data['size_id'] = $size->id;
                    if($player){
                        // update player
                        $player->update($player_data);  
                    } else {
                        // create player
                        $player = Player::create($player_data);
                    }



                    // connect player to space
                    $relation = Relation::where('model1_type', 'SPACE')
                                        ->where('model1_id', $space_id)
                                        ->where('model2_type', 'PLAY')
                                        ->where('model2_id', $player->id)
                                        ->first();
                    if(!$relation){
                        Relation::create([
                            'model1_type' => 'SPACE',
                            'model1_id' => $space_id,
                            'model2_type' => 'PLAY',
                            'model2_id' => $player->id,
                            'type' => 'guest',
                            'notes' => 'imported from csv',
                        ]);
                    }
                } catch (\Throwable $e) {
                    $row['row'] = $i + 2; 
                    $row['error'] = $e->getMessage();
                    $failedRows[] = $row;
                }
            }


            // Jika ada row yang gagal, langsung return CSV dari memory
            if (count($failedRows) > 0) {
                $filename = "failed_import_{$this->routerName}_" . now()->format('Ymd_His') . '.csv';
                
                return $this->eximService->exportCSV(['filename' => $filename], $failedRows);
            }


            return back()->with('success', 'CSV uploaded and processed Successfully!');
        } catch (\Throwable $th) {
            return back()->with('error', 'Failed to import csv. Please try again.' . $th->getMessage());
        }

        return $response;
    } 
}
