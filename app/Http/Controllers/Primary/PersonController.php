<?php

namespace App\Http\Controllers\Primary;
use App\Http\Controllers\Controller;

use App\Models\Space\Person;
use Illuminate\Http\Request;

use Yajra\DataTables\Facades\DataTables;

use App\Models\Primary\Player;



class PersonController extends Controller
{
    /**
     * Display a listing of the persons.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $persons = Person::paginate(10);
        return view('primary.persons.index', compact('persons'));
    }

    /**
     * Show the form for creating a new person.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('primary.persons.create');
    }

    /**
     * Store a newly created person in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:persons',
            'phone_number' => 'required|string|max:20',
            'birth_date' => 'nullable|string|max:255',
            'status' => 'required|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $person = Person::create($validatedData);
        $this->syncPlayer($person);

        return redirect()->route('persons.index')->with('success', 'Person created successfully');
    }
    


    public function syncPlayer($person)
    {
        $player = Player::create(
            [
                'name' => $person->name,
                'size_type' => 'PERS',
                'size_id' => $person->id,
            ]
        );

        return $player;
    }




    public function show($id)
    {
        $person = Person::find($id);
        return view('primary.persons.show', compact('person'));
    }

    /**
     * Show the form for editing the specified person.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $person = Person::find($id);
        return view('primary.persons.edit', compact('person'));
    }

    /**
     * Update the specified person in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {   
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'full_name' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'email' => 'required|string|email|max:255',
            'phone_number' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
            'status' => 'required|string|max:50',
            'notes' => 'nullable|string',
        ]);
        
        $person = Person::find($id);
        $person->update($validatedData);

        $person->generateNumber();
        $person->save();

        return redirect()->route('persons.index')->with('success', 'Person updated successfully');
    }

    /**
     * Remove the specified person from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $person = Person::find($id);
        $person->delete();
        return redirect()->route('persons.index')->with('success', 'Person deleted successfully');
    }



    public function getPersonsData(){
        $persons = Person::with(['player', 'player.user'])->get();

        return DataTables::of($persons)
            ->addColumn('user_username', function ($data) {
                return $data->player?->user?->username ?? 'N/A';
            })
            ->addColumn('actions', function ($data) {
                $route = 'persons';
                
                $actions = [
                    'show' => 'modal',
                    'show_modal' => 'primary.persons.show',
                    'edit' => 'modal',
                    'delete' => 'button',
                ];

                return view('components.crud.partials.actions', compact('data', 'route', 'actions'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }
}
