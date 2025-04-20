<?php

namespace App\Http\Controllers\Space\Player;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

use Yajra\DataTables\Facades\DataTables;

use App\Models\Primary\Player;
use App\Models\Primary\Space;
use App\Models\Primary\Relation;


class SpacePlayerController extends Controller
{
    public function index()
    {
        return view('space.space_players.index');
    }




    public function store(Request $request)
    {
        // dd($request->all());

        $validatedData = $request->validate([
            'space_id' => 'required',
            'player_id' => 'required',
            'type' => 'required|string|max:50',
            'status' => 'required|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $player = Player::findOrFail($request->player_id);
        $relation = Relation::create([
            'model1_type' => 'SPACE',
            'model1_id' => $request->space_id,
            'model2_type' => 'PLAY',
            'model2_id' => $request->player_id,
            'type' => $request->type,
            'status' => $request->status,
            'notes' => $request->notes
        ]);

        return redirect()->route('space_players.index')->with('success', 'Player created successfully');
    }
    



    public function update(Request $request, $id)
    {   
        $validatedData = $request->validate([
            'space_id' => 'required',
            'type' => 'required|string|max:50',
            'status' => 'required|string|max:50',
            'notes' => 'nullable|string',
        ]);
        
        $space_player = Relation::where('model1_type', 'SPACE')
                            ->where('model1_id', $request->space_id)
                            ->where('model2_type', 'PLAY')
                            ->where('model2_id', $id)
                            ->first();
        $space_player->update($validatedData);

        return redirect()->route('space_players.index')->with('success', 'Player updated successfully');
    }


    
    public function destroy($id)
    {
        $space_id = session('space_id') ?? null;

        if($space_id){
            $relation = Relation::where('model1_type', 'SPACE')
                                ->where('model1_id', $space_id)
                                ->where('model2_type', 'PLAY')
                                ->where('model2_id', $id)
                                ->first();
                                
            if ($relation) {
                $relation->delete();
            }
        }

        return redirect()->route('space_players.index')->with('success', 'Player Kicked successfully :)');
    }



    public function getSpacePlayersData(){
        $space_id = Session::get('space_id') ?? null;

        $space = Space::find($space_id);
        $space_players = $space->players;

        return DataTables::of($space_players)
            ->addColumn('size_display', function ($data) {
                return ($data->size_type ?? '?') . ' : ' . ($data->size?->number ?? '?');
            })
            ->addColumn('actions', function ($data) {
                $route = 'space_players';
                
                $actions = [
                    // 'show' => 'modal',
                    // 'show_modal' => 'space.space_players.show',
                    'edit' => 'modal',
                    'delete' => 'button',
                ];

                return view('components.crud.partials.actions', compact('data', 'route', 'actions'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }



    public function search(Request $request)
    {
        $search = $request->q;

        $space_id = $request->space_id;
        $space = Space::with('players')->find($space_id);
        $existing_players = $space->players()->pluck('model2_id')->toArray();

        $players = Player::where(function ($query) use ($search) {
            $query->where('name', 'like', "%$search%")
                ->orWhere('id', 'like', "%$search%");
        })
            ->whereNotIn('id', $existing_players)
            ->orderBy('id', 'desc')
            ->limit(50) // limit hasil
            ->get()
            ->map(function ($player) {
                return [
                    'id' => $player->id,
                    'text' => "{$player->id} - {$player->name}",
                ];
            });

        return response()->json($players);
    }
}
