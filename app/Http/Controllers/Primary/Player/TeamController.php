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



class TeamController extends Controller
{
    public function getData(Request $request){
        $space_id = get_space_id($request, false);

        $query = User::with('player', 'roles', 'permissions');



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

                ->rawColumns(['actions', 'show_roles', 'show_permissions'])
                ->make(true);
        }




        return DataTables::of($query)->make(true);
    } 



    public function searchUser(Request $request)
    {
        $space_id = get_space_id($request, false);

        $search = $request->q;

        $items = User::where(function ($query) use ($search) {
            $query->where('name', 'like', "%$search%")
                ->orWhere('username', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
                ->orWhere('id', 'like', "%$search%");
        });

        if($space_id){
            $items = $items->whereNotIn('id', function ($sub) use ($space_id) {
                $sub->select('model2_id')
                    ->from('relations')
                    ->where('model2_type', 'PLAY')
                    ->where('model1_type', 'SPACE')
                    ->where('model1_id', $space_id);
            });
        }


        $items = $items
            // ->orderBy('id', 'desc')
            ->limit(50) // limit hasil
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => "{$item->username} - {$item->name} : {$item->email}",
                ];
            });

        return response()->json($items);
    }



    public function manageRoles(Request $request){
        $space_id = get_space_id($request);

        $validated = $request->validate([
            'model_type' => 'nullable',
            'model_id' => 'required',
            'role_id' => 'required',
            'action' => 'nullable',
        ]);
        if(!isset($validated['action'])){
            $validated['action'] = 'assignRole';
        }
        if(!isset($validated['model_type'])){
            $validated['model_type'] = 'USER';
        }



        $allowed_models = [
            'USER' => User::class,
            'PLAY' => Player::class,
        ];


        if(!isset($allowed_models[$validated['model_type']])){
            return response()->json(['message' => 'Invalid model type', 'success' => false, 'input' => $request->all], 400);
        }



        $role = Role::findOrFail($validated['role_id']);
        $modelClass = $allowed_models[$validated['model_type']];
        $model = $modelClass::findOrFail($validated['model_id']);

        app(PermissionRegistrar::class)->setPermissionsTeamId($space_id);
        
        
        $allowed_actions = [
            'assign' => 'assignRole',
            'remove' => 'removeRole',
            'sync' => 'syncRoles',
        ];

        if(!isset($allowed_actions[$validated['action']])){
            return response()->json(['message' => 'Invalid action', 'success' => false, 'input' => $request->all], 400);
        }
        
        $method = $allowed_actions[$validated['action']];
        $model->$method($role);



        return response()->json(['message' => 'Role assigned successfully', 'success' => true, 'data' => $model, 'input' => $request->all], 200);
    }




    public function store(Request $request)
    {
        $space_id = get_space_id($request);
    
        return response()->json(['message' => 'Role created successfully', 'success' => true, 'data' => $role], 200);
    }



    public function update(Request $request, String $id)
    {
        $space_id = get_space_id($request);

        try {
            $validated = $request->validate([
                'role_id' => 'required|exists:roles,id',
            ]);

            $user = User::findOrFail($id);

            $role = Role::findOrFail($validated['role_id']);

            app(PermissionRegistrar::class)->setPermissionsTeamId($space_id);

            $user->syncRoles($role);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'success' => false, 'input' => $request->all], 400);
        }


        return response()->json(['message' => 'Team updated successfully', 'success' => true, 'data' => [], 'input' => $request->all], 200);
    }



    public function destroy(String $id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return response()->json(['message' => 'Role deleted successfully', 'success' => true, 'data' => $role], 200);
    }


    public function index(Request $request){
        return view('primary.player.teams.index');
    }
}