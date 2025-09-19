<?php

namespace App\Http\Controllers\Primary;
use App\Http\Controllers\Controller;

use App\Models\Primary\Player;
use App\Models\Primary\Space;
use App\Models\Primary\Relation;
use App\Models\Primary\Transaction;
use App\Models\Primary\Item;

use App\Services\Primary\Basic\EximService;
use App\Services\Primary\Player\PlayerService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use Yajra\DataTables\Facades\DataTables;

use Carbon\Carbon;



class PlayerController extends Controller
{
    protected $eximService;
    protected $playerService;

    public function __construct(EximService $eximService, PlayerService $playerService)
    {
        $this->eximService = $eximService;
        $this->playerService = $playerService;
    }



    // summary
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
        $data = $this->playerService->getSummaryData($data, $txs, $spaces, $validated, $space_id);



        if($request_source == 'api'){
            $data_summary = [];
            if(isset($validated['summary_type']) && isset($data->{$validated['summary_type']})){
                $data_summary = $data->{$validated['summary_type']};
            }

            $spaces_data = $spaces->toArray();


            // itemflow
            if($validated['summary_type'] == 'itemflow'){
                $itemflow = $this->playerService->getSummaryItemflow($txs);
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

        return view('primary.players.summary', compact('data', 'txs', 'spaces', 'list_model_types'));
    }




    // get data
    public function getData(Request $request){
        $request_source = get_request_source($request);
        $user = auth()->user();

        $query = Player::with('type', 'size', 
                                'transactions_as_receiver', 'transactions_as_receiver.details');



        // space
        $space_id = get_space_id($request, false);
        $space = $request->get('space') ?? null;
        if($space && $space_id){
            $spaces = array($space_id);
            
            $query = $query->whereIn('space_id', $spaces);
        }



        // filter model
        $model_type_select = $request->get('model_type_select') ?? 'null';
        if($model_type_select != 'all'){
            if($model_type_select == 'null' || empty($model_type_select)){
                $query->whereDoesntHave('transactions_as_receiver');
            } else {
                $query->whereHas('transactions_as_receiver.details', function($q) use ($model_type_select){
                    $q->where('model_type', $model_type_select);
                });
            }
        } else {
        
        }

        $can_trades_po = $user->can('space.trades.po') ?? false;
        $can_trades_so = $user->can('space.trades.so') ?? false;

        $query = $query->where(function($q) use ($can_trades_po, $can_trades_so){
            if(!$can_trades_po){
                $q->whereNull('tags')
                    ->orWhere('tags', 'not like', '%PO%');
            }

            if(!$can_trades_so){
                $q->whereNull('tags')
                    ->orwhere('tags', 'not like', '%SO%');
            }
        });



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
                ->orWhere('address', 'like', "%{$keyword}%");
            });
        }



        // not in the space
        if($request->filled('not_in_space') && $request->not_in_space == true){
            $space_id = get_space_id($request);
            $query->whereDoesntHave('spaces', function ($q) use ($space_id) {
                $q->where('spaces.id', $space_id);
            });
        }



        $return_type = $request->get('return_type') ?? 'json';
        if($return_type == 'DT'){
            return DataTables::of($query)
                ->addColumn('size_display', function ($data) {
                    return ($data->size_type ?? '?') . ' : ' . ($data->size?->number ?? $data->size?->code ?? '?');
                })
                ->addColumn('actions', function ($data) {
                    $route = 'players';
                    
                    $actions = [
                        'show' => 'modaljs',
                        'edit' => 'modal',
                    ];

                    return view('components.crud.partials.actions', compact('data', 'route', 'actions'))->render();
                })

                ->addColumn('data', function ($data) {
                    return $data;
                })

                ->addColumn('last_transaction', function ($data) {
                    return $data->transactions_as_receiver->first() ?? null;
                })

                ->rawColumns(['actions'])
                ->make(true);
        }


        if($return_type == 'json'){
            return response()->json($query->get());
        }

        // return result
        return DataTables::of($query)->make(true);
    }    



    public function getRelatedSpaces(Request $request){
        $player_id = $request->get('player_id');

        try {
            $player = Player::findOrFail($player_id);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Player not found'], 404);
        }

        $spaces = $player->spacesWithDescendants() ?? [];

        return response()->json(
            [
                'data' => $spaces,
                'success' => true,
            ]
        );
    }


    // Export Import
    public function eximData(Request $request){
        $query = $request->get('query');
        
        try {
            switch($query){
                case 'importTemplate':
                    $response = $this->playerService->getImportTemplate();
                    break;
                case 'export':
                    $response = $this->playerService->exportData($request);
                    break;
                case 'import':
                    $response = $this->playerService->importData($request);
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



    public function index()
    {
        return view('primary.player.players.index');
    }

    

    public function store(Request $request){ return $this->playerService->store($request); }



    public function show(Request $request, $id)
    {
        $data = Player::findOrFail($id);
        return view('primary.player.players.show', compact('data'));
    }



    public function edit($id)
    {
        $player = Player::find($id);
        return view('primary.players.edit', compact('player'));
    }



    public function update(Request $request, $id){ return $this->playerService->update($request, $id); }



    public function storeRelatedPlayer(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'player_id' => 'required',
                'new_player_id' => 'required',
                'type' => 'required|string|max:50',
                'status' => 'required|string|max:50',
                'notes' => 'nullable|string',
            ]);

            $relation = Relation::create([
                'model1_type' => 'PLAY',
                'model1_id' => $request->player_id,
                'model2_type' => 'PLAY',
                'model2_id' => $request->new_player_id,
                'type' => $request->type,
                'status' => $request->status,
                'notes' => $request->notes
            ]);

            return redirect()->route('players.index')->with('success', 'Player Relation created successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    public function updateRelatedPlayer(Request $request, $id)
    {   
        try {
            $validatedData = $request->validate([
                'player_id' => 'required',
                'type' => 'required|string|max:50',
                'status' => 'required|string|max:50',
                'notes' => 'nullable|string',
            ]);

            $relation = Relation::findOrFail($id);
            $relation->update($validatedData);

            return redirect()->route('players.index')->with('success', 'Player Relation updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    public function destroy($id)
    {
        $player = Player::findOrFail($id);
        
        $player->delete();

        return redirect()->route('players.index')->with('success', 'Player deleted successfully');
    }



    public function getPlayersData(){
        $players = Player::with('size', 'type');

        
        // related from player
        $player = session('player_id') ? Player::findOrFail(session('player_id')) : auth()->user()->player;
        $related_players_id = $player->relatedPlayers();
        $players = $players->whereIn('id', $related_players_id);
        
        
        // related from space
        $space_id = session('space_id') ?? null;
        if($space_id){
            
        }


        return DataTables::of($players)
            ->addColumn('size_display', function ($data) {
                return ($data->size_type ?? '?') . ' : ' . ($data->size?->number ?? $data->size?->code ?? '?');
            })
            ->addColumn('actions', function ($data) {
                $route = 'players';
                
                $actions = [
                    'show' => 'modal',
                    'show_modal' => 'primary.players.show',
                    'edit' => 'modal',
                    'delete' => 'button',
                ];

                return view('components.crud.partials.actions', compact('data', 'route', 'actions'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }


    public function getRelatedPlayersData(){
        $relations = Relation::with('model1', 'model2');

        
        // related from player
        $player = session('player_id') ? Player::findOrFail(session('player_id')) : auth()->user()->player;
        $relations = $relations->where('model1_type', 'PLAY')
                                ->where('model2_type', 'PLAY')
                                ->where('model1_id', $player->id);
        
        
        // related from space
        $space_id = session('space_id') ?? null;
        if($space_id){
            
        }


        return DataTables::of($relations)
            ->addColumn('size_display', function ($data) use ($player) {
                $model = ($data->model1_id == $player->id) ? $data->model2 : $data->model1;

                $model->size_display = ($model->size_type ?? '?') . ' : ' . ($model->size?->number ?? $model->size?->code ?? '?');

                return $model->size_display;
            })
            ->addColumn('actions', function ($data) {
                $route = 'players';
                
                $actions = [
                    'show' => 'modal',
                    'show_modal' => 'primary.players.show',
                    'edit' => 'modal',
                ];

                return view('components.crud.partials.actions', compact('data', 'route', 'actions'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }


    public function searchPlayer(Request $request)
    {
        $search = $request->q;

        $player_id = $request->player_id;
        $players = Player::where(function ($query) use ($search) {
            $query->where('name', 'like', "%$search%")
                ->orWhere('code', 'like', "%$search%")
                ->orWhere('id', 'like', "%$search%");
        })
            ->where('id', '!=', $player_id)
            ->orderBy('id', 'desc')
            ->limit(50) // limit hasil
            ->get()
            ->map(function ($player) {
                return [
                    'id' => $player->id,
                    'text' => "{($player->code ?? $player->id)} - {$player->name} - {$player->size_type} : {$player->size?->number}",
                ];
            });

        return response()->json($players);
    }


    public function switchPlayer(Request $request, $player_id)
    {
		// forget player before
		$this->forgetSession();
        $this->forgetSession('space');

		$player = Player::with('size', 'type')->findOrFail($player_id);

        Session::put('player_id', $player->id);
		Session::put('player_name', $player->name);
		Session::put('layout', 'space');

        return redirect()->route('dashboard_space')->with('success', "Anda masuk ke {$player->name}");
    }



    public function forgetSession($ikey = 'player')
	{
		// to forget from what player had
		foreach(session()->all() as $key => $value) {
			if(str_contains($key, $ikey)) {
				session()->forget($key);				
			}
		}
	}


    public function exitPlayer(Request $request, $route = 'lobby')
	{
        // Hapus session space
        $this->forgetSession();
        $this->forgetSession('space');

        // change layout to space
        Session::put('layout', 'space');

        // Redirect ke halaman space (atau dashboard utama)
        return redirect()->route($route)->with('status', 'You have exited the player & the space.');
	}
}
