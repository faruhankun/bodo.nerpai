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
        'size_type (*) (PERS/GRP)',
        'size_id (id)',
        'code', 
        'name (*)',
        'full_name',
        'address (json)',
        'birth_date',
        'death_date',
        'email (*)',
        'phone_number (*)',
        'gender',
        'id_card_number',
        'notes',
    ];

    protected $routerName = 'players';


    public function __construct(EximService $eximService)
    {
        $this->eximService = $eximService;
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
        $space_id = $this->getSpaceId($request);

        $query = Player::with('type', 'size')
                        ->whereIn('id', function ($query) use ($space_id) {
                            $query->select('model2_id')
                                ->from('relations')
                                ->where('model2_type', 'PLY')
                                ->where('model1_type', 'SPACE')
                                ->where('model1_id', $space_id);
                        });

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

            $row['address'] = $collect->address ?? '';
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
                        'size_type' => $row['size_type (*) (PERS/GRP)'] ?? 'PERS',
                        'size_id' => $row['size_id (id)'] ?? null,
                        'name' => $row['name (*)'] ?? '',
                        'address' => $row['address (json)'] ?? '',
                        'notes' => $row['notes'] ?? null,
                    ];

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
                    ];



                    // look for size
                    $size = null;
                    if(!empty($player_data['size_id'])){
                        if($player_data['size_type'] == 'PERS'){
                            $size = Person::find($player_data['size_id']);
                        } else {
                            $size = Group::find($player_data['size_id']);
                        }

                        if($size){
                            // update size
                            $size->update($size_data);
                        } else {
                            // create size
                            if($player_data['size_type'] == 'PERS'){
                                $size = Person::create($size_data);
                            } else {
                                $size = Group::create($size_data);
                            }
                        }
                    } else {
                        // create size
                        if($player_data['size_type'] == 'PERS'){
                            $size = Person::create($size_data);
                        } else {
                            $size = Group::create($size_data);
                        }
                        $player_data['size_id'] = $size->id;
                    }



                    // look for player
                    $player = Player::with('type', 'size')
                                    ->join('persons', 'players.size_id', '=', 'persons.id')
                                    ->where('persons.name', $player_data['name'])
                                    ->orWhere('persons.email', $size_data['email'])
                                    ->orWhere('persons.phone_number', $size_data['phone_number'])
                                    ->first();

                    if($player){
                        // update player
                        $player->update($player_data);  
                    } else {
                        // create player
                        $player_data['size_id'] = $size->id;
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


            return redirect()->route("{$this->routerName}.index")->with('success', 'CSV uploaded and processed Successfully!');
        } catch (\Throwable $th) {
            return back()->with('error', 'Failed to import csv. Please try again.' . $th->getMessage());
        }

        return $response;
    } 
}
