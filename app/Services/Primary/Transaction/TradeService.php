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

    public function __construct(EximService $eximService
                                , SpaceService $spaceService)
    {
        $this->eximService = $eximService;
        $this->spaceService = $spaceService;
    }



    // crud
    public function updateData($tx, $data, $details = [])
    {
        // Update main journal entry
        $tx->update($data);

        // Delete old details
        $tx->details()->delete();

        // Create new details
        $journalDetails = [];
        foreach ($details as $detail) {
            $journalDetails[] = [
                'transaction_id' => $tx->id,
                'detail_type' => 'ITM',
                'detail_id' => $detail['detail_id'],
                'quantity' => $detail['quantity'] ?? 1,
                'price' => $detail['price'] ?? 0,
                'balance' => ($detail['price'] ?? 0) *(1 - ($detail['discount'] ?? 0 ) / 100),
                'cost_per_unit' => $detail['cost_per_unit'] ?? 0,
                'notes' => $detail['notes'] ?? null,
            ];
        }

        $tx->details()->createMany($journalDetails);

        
        if($tx->number == null){
            $tx->generateNumber();
            $tx->save();
        }

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
