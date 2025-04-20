<?php

namespace App\Http\Controllers\Primary;
use App\Http\Controllers\Controller;

use App\Models\Primary\Space;

use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Yajra\DataTables\Facades\DataTables;

class SpaceController extends Controller
{
    /**
     * Display a listing of the spaces.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $spaces = Space::paginate(10);

        return view('primary.spaces.index', compact('spaces'));
    }

    /**
     * Show the form for creating a new space.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('primary.spaces.create');
    }

    /**
     * Store a newly created space in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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
        $space = Space::find($id);
        return view('primary.spaces.show', compact('space'));
    }

    public function edit($id)
    {
        $space = Space::find($id);
        return view('primary.spaces.edit', compact('space'));
    }



    public function update(Request $request, $id)
    {   
        $validatedData = $request->validate([
            'code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'type_type' => 'required|string|max:255',
            'status' => 'required|string|max:50',
            'notes' => 'nullable|string',
        ]);
        
        $space = Space::find($id);
        $space->update($validatedData);
        $space->save();

        return redirect()->route('spaces.index')->with('success', 'Space updated successfully');
    }



    public function destroy($id)
    {
        $space = Space::find($id);
        $space->delete();
        return redirect()->route('spaces.index')->with('success', 'Space deleted successfully');
    }



    public function getSpacesData(Request $request){
        $space_id = Session::get('space_id');
        $player = Auth::user()->player;
        
        $spaces = $player->spaces;
        if($space_id){
            $spaces = $player->spaces()
                        ->where('parent_type', 'SPACE')
                        ->where('parent_id', $space_id)
                        ->get();
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
        
        if($parent_id && $parent_type){
            if($parent_type == 'SPACE'){
                return $this->switchSpace($request, $parent_id);
            }
        } else {
            // Hapus session space
            $this->forgetSpace();

            // change layout to lobby
            Session::put('layout', 'lobby');

            // Redirect ke halaman lobby (atau dashboard utama)
            return redirect()->route($route)->with('status', 'You have exited the space.');
        }
	}
}
