<?php

namespace App\Http\Controllers\Primary;
use App\Http\Controllers\Controller;

use App\Models\Primary\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use Yajra\DataTables\Facades\DataTables;

class PlayerController extends Controller
{
    /**
     * Display a listing of the players.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $players = Player::paginate(10);
        return view('primary.players.index', compact('players'));
    }

    /**
     * Show the form for creating a new player.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('primary.players.create');
    }

    /**
     * Store a newly created player in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:players',
            'phone_number' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
            'status' => 'required|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $player = Player::create($validatedData);
        return redirect()->route('players.index')->with('success', 'Player created successfully');
    }
    /**
     * Display the specified player.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $player = Player::find($id);
        return view('primary.players.show', compact('player'));
    }



    public function edit($id)
    {
        $player = Player::find($id);
        return view('primary.players.edit', compact('player'));
    }



    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'status' => 'required|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $player = Player::find($id);
        $player->update($validatedData);

        return redirect()->route('players.index')->with('success', 'Player updated successfully');
    }



    public function destroy($id)
    {
        $player = Player::find($id);
        $player->delete();
        return redirect()->route('players.index')->with('success', 'Player deleted successfully');
    }



    public function getPlayersData(){
        $players = Player::with('size', 'type');

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
