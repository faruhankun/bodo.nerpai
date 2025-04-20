<?php

namespace App\Http\Controllers\Primary;
use App\Http\Controllers\Controller;

use App\Models\Primary\Group;
use Illuminate\Http\Request;

use Yajra\DataTables\Facades\DataTables;

use App\Models\Primary\Player;



class GroupController extends Controller
{
    public function index()
    {
        return view('primary.groups.index');
    }




    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'status' => 'required|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $group = Group::create($validatedData);
        $this->syncPlayer($group);

        return redirect()->route('groups.index')->with('success', 'Group created successfully');
    }
    


    public function syncPlayer($group)
    {
        $player = Player::create(
            [
                'name' => $group->name,
                'size_type' => 'GRP',
                'size_id' => $group->id,
            ]
        );

        return $player;
    }



    public function update(Request $request, $id)
    {   
        $validatedData = $request->validate([
            'code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'status' => 'required|string|max:50',
            'notes' => 'nullable|string',
        ]);
        
        $group = Group::find($id);
        $group->update($validatedData);

        
        // update player
        $group->player->update([
            'code' => $group->code,
            'name' => $group->name,
        ]);
        $group->player->save();


        return redirect()->route('groups.index')->with('success', 'Group updated successfully');
    }



    public function destroy($id)
    {
        $group = Group::find($id);
        $group->delete();
        return redirect()->route('groups.index')->with('success', 'Group deleted successfully');
    }



    public function getGroupsData(){
        $groups = [];

        $player = auth()->user()->player;
        $spaceIds = $player->spaces()->pluck('model1_id')->toArray();

        $players = Player::whereHas('spaces', function ($query) use ($spaceIds) {
                $query->whereIn('model1_id', $spaceIds)
                        ->where('size_type', 'GRP');
            })
            ->where('size_id', '!=', null)
            ->distinct()
            ->get();

        $groups = $players->map(function ($player) {
            return $player->size;
        });

        return DataTables::of($groups)
            ->addColumn('actions', function ($data) {
                $route = 'groups';
                
                $actions = [
                    'show' => 'modal',
                    'show_modal' => 'primary.groups.show',
                    'edit' => 'modal',
                    'delete' => 'button',
                ];

                return view('components.crud.partials.actions', compact('data', 'route', 'actions'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }
}
