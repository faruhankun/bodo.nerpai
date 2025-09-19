<?php
namespace App\Http\Controllers\Primary\Player;

use App\Http\Controllers\Controller;

// use App\Http\Controllers\Permission;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

use Illuminate\Support\Str;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

use App\Models\User;
use App\Models\Primary\Player;
use App\Models\Primary\Space;
use App\Models\Primary\Relation;



class TeamController extends Controller
{
    public function getData(Request $request){
        $space_id = get_space_id($request, false);

        $query = User::with('player', 'roles', 'permissions');

        $query->with('player.space_relations', function ($q) use ($space_id) {
            $q->where('model1_id', $space_id)
                ->limit(1);
        });



        $space = $request->get('space') ?? null;
        if($space_id){
            $query = $query->whereIn('player_id', function ($sub) use ($space_id) {
                        $sub->select('model2_id')
                            ->from('relations')
                            ->where('model2_type', 'PLAY')
                            ->where('model1_type', 'SPACE')
                            ->where('model1_id', $space_id);
                    });
        }



        // space
        $roles = $request->get('roles') ?? null;
        if($roles){
            if($roles == 'space' && !$space_id){
                return response()->json(['message' => 'Space not found', 'success' => false], 500);
            }
            if($roles == 'space' && $space_id){                
                app(PermissionRegistrar::class)->setPermissionsTeamId($space_id);

                $query = $query->with(['roles' => function ($q) use ($space_id) {

                }]);
            } else {
                $query = $query->with('roles');
            }
        }



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
                ->orWhere('code', 'like', "%{$keyword}%")
                ->orWhere('id', 'like', "%{$keyword}%")
                ->orWhere('notes', 'like', "%{$keyword}%");
            });
        }



        // order by id desc by default
        $orderby = $request->get('orderby');
        $orderdir = $request->get('orderdir');
        if($orderby && $orderdir){
            $query->orderBy($orderby, $orderdir);
        } else {
            $query->orderBy('id', 'desc');
        }



        // return datatable
        $return_type = $request->get('return_type');
        if($return_type && $return_type == 'DT'){
            return DataTables::of($query)
                ->addColumn('actions', function ($data) {
                    $route = 'teams';
                    
                    $actions = [
                        'edit' => 'modal',
                        'delete' => 'button',
                        'delete_id' => $data->player_id ?? '',
                    ];

                    return view('components.crud.partials.actions', compact('data', 'route', 'actions'))->render();
                })
                
                ->addColumn('show_roles', function ($data) {
                    $space_roles = $data->roles ?? collect();

                    return $space_roles->map(function ($role) {
                        return '<span
                                    class="inline-block px-2 py-1 bg-blue-100 text-blue-600 text-xs font-medium rounded-lg mr-1 mb-1">
                                    '. $role->name .'
                                </span>';
                    })->implode(' '); 
                })

                ->addColumn('show_permissions', function ($data) {
                    $space_roles = $data->roles ?? collect();
                    $space_permissions = $space_roles->first()->permissions ?? collect();
                
                    return $space_permissions->map(function ($permission) {
                        return '<span
                                    class="inline-block px-2 py-1 bg-blue-100 text-blue-600 text-xs font-medium rounded-lg mr-1 mb-1">
                                    '. $permission->name .'
                                </span>';
                    })->implode(' '); 
                })

                ->addColumn('relation_type', function ($data) use ($space_id) {
                    return $data->player->space_relations
                                ->where('model1_id', $space_id)
                                ->first()
                                ->type ?? '';
                })

                ->addColumn('relation_notes', function ($data) use ($space_id) {
                    return $data->player->space_relations
                                ->where('model1_id', $space_id)
                                ->first()
                                ->notes ?? '';
                })

                // ->addColumn('relation', function ($data) use ($space_id) {
                //     return $data->player->space_relations
                //                 ->where('model1_id', $space_id)
                //                 ->first() ?? [];
                // })

                ->rawColumns(['actions', 'show_roles', 'show_permissions'])
                ->make(true);
        }




        return DataTables::of($query)->make(true);
    } 



    public function searchUser(Request $request)
    {
        $space_id = get_space_id($request, false);

        $search = $request->q;

        $users = User::where(function ($query) use ($search) {
            $query->where('name', 'like', "%$search%")
                ->orWhere('username', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
                ->orWhere('id', 'like', "%$search%");
        });


        if($space_id){
            $users = $users->whereNotIn('player_id', function ($sub) use ($space_id) {
                $sub->select('model2_id')
                    ->from('relations')
                    ->where('model2_type', 'PLAY')
                    ->where('model1_type', 'SPACE')
                    ->where('model1_id', $space_id);
            });
        }
        

        $users = $users
            // ->orderBy('id', 'desc')
            ->limit(50) // limit hasil
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => "{$item->username} - {$item->name} : {$item->email}",
                ];
            });

        return response()->json($users);
    }



    public function store(Request $request)
    {
        $space_id = get_space_id($request);


        $validatedData = $request->validate([
            'user_id' => 'required',
            'type' => 'required|string|max:50',
            'status' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);
        $validatedData['status'] = $validatedData['status'] ?? 'active';

        $user = User::findOrFail($request->user_id);
        $player_id = $user?->player_id;



        $relation = [];
        try {
            $relation = Relation::updateOrCreate(
                [
                    'model1_type' => 'SPACE',
                    'model1_id' => $space_id,
                    'model2_type' => 'PLAY',
                    'model2_id' => $player_id,
                ],
                [
                    'type' => $validatedData['type'],
                    'status' => $validatedData['status'],
                    'notes' => $validatedData['notes'],
                ]
            );
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'success' => false], 400);
        }

    

        return response()->json(['message' => 'User assigned successfully', 'success' => true, 'data' => $relation], 200);
    }



    public function update(Request $request, String $id)
    {
        $space_id = get_space_id($request);


        $relation = [];
        try {
            $validated = $request->validate([
                'role_id' => 'nullable|exists:roles,id',

                'type' => 'nullable',
                'notes' => 'nullable',
            ]);



            $user = User::findOrFail($id);


            if(isset($validated['role_id'])){
                $role = Role::findOrFail($validated['role_id']);
                
                app(PermissionRegistrar::class)->setPermissionsTeamId($space_id);
    
                $user->syncRoles($role);
            }


            // update relation
            $relation = Relation::updateOrCreate(
                [
                    'model1_type' => 'SPACE',
                    'model1_id' => $space_id,
                    'model2_type' => 'PLAY',
                    'model2_id' => $user->player_id,
                ],
                [
                    'type' => $validated['type'],
                    'notes' => $validated['notes']
                ]
            );

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'success' => false, 'input' => $request->all], 400);
        }


        return response()->json(['message' => 'Team updated successfully', 'success' => true, 'data' => $relation, 'input' => $request->all], 200);
    }



    public function destroy(Request $request, String $id)
    {
        $request_source = get_request_source($request);
        $space_id = get_space_id($request);


        $relations = [];
        try {
            $space_and_children = Space::find($space_id)->AllChildren()->pluck('id')->toArray();
            $space_and_children = array_merge($space_and_children, [$space_id]);

            $relations = Relation::where('model1_type', 'SPACE')
                                ->whereIn('model1_id', $space_and_children)
                                ->where('model2_type', 'PLAY')
                                ->where('model2_id', $id)
                                ->get();

            foreach ($relations as $relation) {
                $relation->delete();
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'success' => false, 'input' => $request->all], 400);
        }


        if($request_source == 'web'){
            return back()->with('success', 'Team kicked successfully :D');
        }


        return response()->json(['message' => 'Team kicked successfully :D', 'success' => true, 'data' => $relations, 'input' => $request->all], 200);
    }


    public function index(Request $request){
        return view('primary.player.teams.index');
    }
}