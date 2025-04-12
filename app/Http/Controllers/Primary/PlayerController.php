<?php

namespace App\Http\Controllers\Primary;
use App\Http\Controllers\Controller;

use App\Models\Primary\Player;
use Illuminate\Http\Request;

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

    /**
     * Show the form for editing the specified player.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $player = Player::find($id);
        return view('primary.players.edit', compact('player'));
    }

    /**
     * Update the specified player in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Remove the specified player from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $player = Player::find($id);
        $player->delete();
        return redirect()->route('players.index')->with('success', 'Player deleted successfully');
    }



    public function getPlayersData(){
        $players = Player::with('size', 'type')->get();

        return DataTables::of($players)
            ->addColumn('size_display', function ($data) {
                return ($data->size_type ?? '?') . ' : ' . ($data->size?->number ?? '?');
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
}
