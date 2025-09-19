<?php

namespace App\Services\Primary\Player;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

use App\Services\Primary\Basic\EximService;

use App\Models\Primary\Player;
use App\Models\Primary\Person;
use App\Models\Primary\Group;
use App\Models\Primary\Relation;


class PlayerService
{
    protected $eximService;
    protected $import_columns = [
        'code', 
        'name',
        'address_detail',
        'email',
        'phone_number',
        'notes',
    ];

    protected $routerName = 'players';


    public function __construct(EximService $eximService)
    {
        $this->eximService = $eximService;
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




    public function update(Request $request, $id){
        try {
            $validated = $request->validate([
                'name' => 'nullable|string|max:255',
                'code' => 'nullable|string|max:255',
                'email' => 'nullable|string|email',
                'phone_number' => 'nullable|string|max:20',
                'status' => 'nullable|string',
                'notes' => 'nullable|string',

                'address' => 'nullable|array',
                'address.*' => 'nullable|string',
                'province' => 'nullable|string',
                'regency' => 'nullable|string',
                'district' => 'nullable|string',
                'village' => 'nullable|string',
                'postal_code' => 'nullable|string',
                'address_detail' => 'nullable|string',

                'shopee_username' => 'nullable|string',
                'tokopedia_username' => 'nullable|string',
                'whatsapp_number' => 'nullable|string',

                'tags' => 'nullable|string',
                'links' => 'nullable|string',
            ]);

            $validated['tags'] = json_decode($validated['tags'], true) ?: [];
            $validated['links'] = json_decode($validated['links'], true) ?: [];


            $player = Player::findOrFail($id);
            $player->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Player updated successfully',
                'data' => $player,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }



    public function store(Request $request){
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'nullable|string|max:255',
                'email' => 'nullable|string|email',
                'phone_number' => 'nullable|string|max:20',
                'status' => 'nullable|string|max:50',
                'notes' => 'nullable|string',
            ]);

            $space_id = get_space_id($request);
            $validated['space_type'] = 'SPACE';
            $validated['space_id'] = $space_id;


            $player = Player::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Player created successfully',
                'data' => $player,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }




    public function getImportTemplate(){
        $response = $this->eximService->exportCSV(['filename' => "{$this->routerName}_import_template.csv"], $this->import_columns);

        return $response;
    }


    public function getSpaceId(Request $request)
    {
        $space_id = $request->space_id ?? (session('space_id') ?? null);
        if(is_null($space_id)){
            abort(403);
        }

        return $space_id;
    }

    public function getQueryData(Request $request)
    {
        $space_id = get_space_id($request);

        $query = Player::with('type', 'size',
                                'transactions_as_receiver', 'transactions_as_receiver.details')
                        ->where('space_id', $space_id);


        
        // transaction
        $model_type_select = $request->get('model_type_select') ?? 'null';
        if($model_type_select != 'all'){
            if($model_type_select == 'null'){
                $query->whereDoesntHave('transactions_as_receiver');
            } else {
                $query->whereHas('transactions_as_receiver.details', function($q) use ($model_type_select){
                    $q->where('model_type', $model_type_select);
                });
            }
        }                



        return $query;
    }


    public function exportData(Request $request)
    {
        $params = json_decode($request->get('params'), true);

        $query = $this->getQueryData($request);
        // search & order filter
        $query = $this->eximService->exportQuery($query, $params, ['code', 'name', 'size_type', 'notes']);


        
        $query->take(10000);
        $collects = $query->get();


        // Prepare the CSV data
        $filename = "export_{$this->routerName}_" . now()->format('Ymd_His') . '.csv';
        $data = collect();

        // fetch transation into array
        // grouped by number
        foreach($collects as $collect){
            $row = [];

            $row['size_type'] = $collect->size_type;
            $row['size_id'] = $collect->size_id;
            $row['code'] = $collect->code;
            
            $row['name'] = $collect->name ?? '';
            $row['full_name'] = $collect->full_name ?? '';

            $row['email'] = $collect->email ?? '';
            $row['phone_number'] = $collect->phone_number ?? '';

            $row['address'] = $collect->address ? (is_array($collect->address) ? implode(', ', $collect->address) : $collect->address) : '';
            $row['birth_date'] = $collect->birth_date ?? '';
            $row['death_date'] = $collect->death_date ?? '';

            $row['gender'] = $collect->gender ?? '';
            $row['id_card_number'] = $collect->id_card_number ?? '';
            
            $row['status'] = $collect->status;
            $row['notes'] = $collect->notes;

            $data[] = $row;
        }



        $response = $this->eximService->exportCSV(['filename' => $filename], $data);

        return $response;
    }


    public function importData(Request $request)
    {
        $space_id = get_space_id($request);

        DB::beginTransaction();


        try {
            $validated = $request->validate([
                'file' => 'required|mimes:csv,txt'
            ]);


            $file = $validated['file'];
            $data = collect();
            $failedRows = collect();
            $requiredHeaders = ['name'];


            // Read the CSV into an array of associative rows
            $data = $this->eximService->convertCSVtoArray($file, ['requiredHeaders' => $requiredHeaders]);

            // dd($data);
            
            // process data
            foreach($data as $i => $row){
                try {
                    // skip if no code or name
                    if (empty($row['name'])) {
                        throw new \Exception('Missing required field: name');
                    }


                    $player_data = [];

                    if(isset($row['code']) && !empty($row['code'])){
                        $player_data['code'] = $row['code'] ?? null;
                    }

                    if(isset($row['name']) && !empty($row['name'])){
                        $player_data['name'] = $row['name'] ?? null;
                    }

                    if(isset($row['address']) && !empty($row['address'])){
                        $player_data['address'] = $row['address'] ?? null;
                    }

                    if(isset($row['email']) && !empty($row['email'])){
                        $player_data['email'] = $row['email'] ?? null;
                    }

                    if(isset($row['phone_number']) && !empty($row['phone_number'])){
                        $player_data['phone_number'] = $row['phone_number'] ?? null;
                    }

                    if(isset($row['notes']) && !empty($row['notes'])){
                        $player_data['notes'] = $row['notes'] ?? null;
                    }

                    if(isset($row['status']) && !empty($row['status'])){
                        $player_data['status'] = $row['status'] ?? null;
                    }

                    if(isset($row['address_detail']) && !empty($row['address_detail'])){
                        $player_data['address_detail'] = $row['address_detail'] ?? null;
                    }

                    if(isset($row['created_at']) && !empty($row['created_at'])){
                        $player_data['created_at'] = $row['created_at'];
                    }

                    if(isset($row['tags']) && !empty($row['tags'])){
                        $tags = json_decode($row['tags'], true) ?? [];
                        $player_data['tags'] = $tags;
                    }

                    if(isset($row['links']) && !empty($row['links'])){
                        $links = json_decode($row['links'], true) ?? [];
                        $player_data['links'] = $links;
                    }



                    $player_data['space_type'] = 'SPACE';
                    $player_data['space_id'] = $space_id;



                    $player = Player::updateOrCreate([
                        'space_type' => 'SPACE',
                        'space_id' => $space_id,
                        'code' => $player_data['code'],
                        'name' => $player_data['name'],
                    ], $player_data);
                } catch (\Throwable $e) {
                    $row['row'] = $i + 2; 
                    $row['error'] = $e->getMessage();
                    $failedRows[] = $row;
                }
            }


            // Jika ada row yang gagal, langsung return CSV dari memory
            if (count($failedRows) > 0) {
                DB::rollBack();

                $filename = "failed_import_{$this->routerName}_" . now()->format('Ymd_His') . '.csv';
                
                return $this->eximService->exportCSV(['filename' => $filename], $failedRows);
            }
        } catch (\Throwable $th) {
            DB::rollBack();

            return back()->with('error', 'Failed to import csv. Please try again.' . $th->getMessage());
        }


        DB::commit();

        return back()->with('success', 'Successfully imported csv :D');
    } 
}
