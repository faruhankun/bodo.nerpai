<?php

namespace App\Http\Controllers\Primary;
use App\Http\Controllers\Controller;

use App\Models\Primary\Space;
use App\Models\Primary\Relation;
use App\Models\Primary\Player;

use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Yajra\DataTables\Facades\DataTables;

class SpaceController extends Controller
{
    public function search(Request $request){
        $include_self = $request->get("include_self") ?? false;
        $player_id = get_player_id($request);
        $space_id = get_space_id($request, false);


        $query = Space::with('parent', 'children');

        $keyword = $request->get('q');
        if($keyword){
            $query->where(function($q) use ($keyword){
                $q->where('code', 'like', "%{$keyword}%")
                ->orWhere('name', 'like', "%{$keyword}%")
                ->orWhere('notes', 'like', "%{$keyword}%");
            });
        }


        // include self
        if(!$include_self){
            if($space_id){
                $query->whereNotIn('id', [$space_id]);
            }
        }


        // player
        if($player_id){
            $query->whereIn('id', function ($sub) use ($player_id) {
                $sub->select('model1_id')
                    ->from('relations')
                    ->where('model1_type', 'SPACE')
                    ->where('model2_type', 'PLAY')
                    ->where('model2_id', $player_id)
                    ->where('type', '!=', 'guest');
            });
        }


        $limit = $request->get('limit');
        if($limit){
            if($limit != 'all'){
                $query->limit($limit);
            }
        } else {
            $query->limit(50);
        }

        $query->orderBy('code', 'asc');

        return DataTables::of($query)
            ->make(true);
    }



    public function getSpacesDT(Request $request){
        $space_id = get_space_id($request, false);
        $include_self = $request->get("include_self") ?? false;
        $include_parent = $request->get("include_parent") ?? false;

        $player_id = get_player_id($request, false);
        
        if(!$space_id && !$player_id){
            return response()->json(['error' => 'require space_id OR player_id'], 403);
        }

        $query = Space::with('parent', 'children');

        $query->where(function($q) use ($space_id, $player_id, $include_parent){
            // children
            if($space_id){
                $q->where(function ($q2) use ($space_id, $include_parent) {
                    $q2->where('parent_type', 'SPACE')
                        ->where('parent_id', $space_id);

                    if($include_parent){
                        $q2->orWhere('id', function ($q3) use ($space_id) {
                            $q3->select('parent_id')
                                ->from('spaces')
                                ->where('id', $space_id)
                                ->limit(1);
                        });
                    }
                });
            }

            if($player_id){
                $q->whereIn('id', function ($sub) use ($player_id) {
                    $sub->select('model1_id')
                        ->from('relations')
                        ->where('model1_type', 'SPACE')
                        ->where('model2_type', 'PLAY')
                        ->where('model2_id', $player_id)
                        ->where('type', '!=', 'guest');
                });
            }
        });

        
        if($include_self){
            $query->orWhere('id', $space_id);
        }

        return DataTables::of($query)
            ->make(true);
    }



    public function index()
    {
        $spaces = Space::paginate(10);

        $space_id = session('space_id') ?? null;

        if($space_id){
            $space = Space::find($space_id);
        }

        return view('primary.spaces.index', compact('spaces'));
    }

    

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'type_type' => 'required|string|max:255',
            'status' => 'required|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $space = Space::create($validatedData);

        $space->parent_type = 'SPACE';
        $parent_id = Session::get('space_id') ?? null;
        $space->parent_id = $parent_id;
        $space->save();


        // Space owner
        $player = Auth::user()->player;
        DB::table('relations')->insert([
            'model1_type' => 'SPACE',
            'model1_id' => $space->id,
            'model2_type' => 'PLAY',
            'model2_id' => $player->id,
            'type' => 'owner',
        ]);


        return redirect()->route('spaces.index')->with('success', 'Space created successfully');
    }

    public function show($id)
    {
        try {
            $space = Space::findOrFail($id);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'success' => false], 500);
        }

        return response()->json(['data' => array($space), 'success' => true, 'recordsFiltered' => 1]);
    }

    public function edit($id)
    {
        $space = Space::find($id);
        return view('primary.spaces.edit', compact('space'));
    }



    public function update(Request $request, $id)
    {   
        $request_source = $request->input('request_source') ?? 'api';

        try {
            $validatedData = $request->validate([
                'code' => 'required|string|max:255',
                'name' => 'required|string|max:255',
                // 'type_type' => 'required|string|max:255',
                // 'status' => 'required|string|max:50',
                'notes' => 'nullable|string',
            ]);
            
            $space = Space::find($id);
            $space->update($validatedData);
            $space->save();
        } catch (\Throwable $th) {
            if($request_source == 'api'){
                return response()->json(['error' => $th->getMessage(), 'success' => false], 500);
            }

            return redirect()->route('spaces.index')->with('error', $th->getMessage());
        }

        if($request_source == 'api'){
            return response()->json(['success' => true, 'message' => 'Space updated successfully', 'data' => $space]);
        }

        return redirect()->route('spaces.index')->with('success', 'Space updated successfully');
    }



    public function destroy(Request $request, String $id)
    {
        $request_source = $request->input('request_source') ?? 'api';
        $space = Space::find($id);
        
        if($space->children()->count() > 0){
            if($request_source == 'api'){
                return response()->json(['message' => 'Cannot delete space with children', 'success' => false], 500);
            }

            return redirect()->route('spaces.index')->with('error', 'Cannot delete space with children');
        }
        
        // delete all relations
        Relation::where('model1_type', 'SPACE')
                ->where('model1_id', $space->id)
                ->delete();

        $space->delete();

        if($request_source == 'api'){
            return response()->json(['success' => true, 'message' => 'Space deleted successfully', 'data' => $space]);
        }

        return redirect()->route('spaces.index')->with('success', 'Space deleted successfully');
    }



    public function getSpacesData(Request $request){
        $space_id = get_space_id($request, false);
        $player_id = session('player_id') ?? Auth::user()->player_id;
        
        $player = Player::findOrFail($player_id);

        $spaces = $player->spacesWithDescendants();
        if($space_id){
            $spaces = $player->spacesWithDescendants()
                        ->where('parent_type', 'SPACE')
                        ->where('parent_id', $space_id);
        } else {
            // $spaces = Space::with(['parent', 'type'])->get();
        }

        return DataTables::of($spaces)
            ->addColumn('parent_display', function ($data) {
                return ($data->parent_type ?? '?') . ' : ' . ($data->parent?->code ?? '?');
            })
            ->addColumn('type_display', function ($data) {
                return ($data->type_type ?? '?') . ' : ' . ($data->type?->code ?? '?');
            })
            ->addColumn('actions', function ($data) {
                $route = 'spaces';
                
                $actions = [
                    'show' => 'modal',
                    'show_modal' => 'primary.spaces.show',
                    'edit' => 'modal',
                    'delete' => 'button',
                ];

                return view('components.crud.partials.actions', compact('data', 'route', 'actions'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }



    public function switchSpace(Request $request, $space_id)
    {
		// forget space before
		$this->forgetSpace();

		$space = Space::findOrFail($space_id);

        // Simpan informasi perusahaan yang dipilih di session
        if($space->parent_type == 'SPACE'){
            Session::put('space_parent_type', $space->parent_type);
            if($space->parent_id){
                Session::put('space_parent_id', $space->parent_id);
            }
        }

        Session::put('space_id', $space->id);
		Session::put('space_name', $space->name);
		Session::put('layout', 'space');

        return redirect()->route('dashboard_space')->with('success', "Anda masuk ke {$space->name}");
    }



    public function forgetSpace()
	{
		// to forget from what space had
		foreach(session()->all() as $key => $value) {
			if(str_contains($key, 'space')) {
				session()->forget($key);				
			}
		}
	}



    public function exitSpace(Request $request, $route = 'lobby')
	{
        $parent_type = Session::get('space_parent_type') ?? null;
        $parent_id = Session::get('space_parent_id') ?? null;
        
        // if($parent_id && $parent_type){
        //     if($parent_type == 'SPACE'){
        //         return $this->switchSpace($request, $parent_id);
        //     }
        // } else {
            // Hapus session space
            $this->forgetSpace();

            // change layout to lobby
            Session::put('layout', 'lobby');

            // Redirect ke halaman lobby (atau dashboard utama)
            return redirect()->route($route)->with('status', 'You have exited the space.');
        }
	// }
}
