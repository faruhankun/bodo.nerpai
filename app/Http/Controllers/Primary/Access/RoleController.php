<?php
namespace App\Http\Controllers\Primary\Access;

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



class RoleController extends Controller
{
    public function getData(Request $request){
        $space_id = get_space_id($request, false);

        $query = Role::query()
                    ->with('permissions')
                    ->where('team_id', $space_id);


        // space
        $guard_name = $request->get('guard_name') ?? 'space';
        if($guard_name){
            if($guard_name == 'space' && !$space_id){
                return response()->json(['message' => 'Space not found', 'success' => false], 500);
            }
            if($guard_name != 'all'){
                $query->where('team_id', $space_id);
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
                    $route = 'roles';
                    
                    $actions = [
                        'edit' => 'modal',
                        // 'delete' => 'button',
                    ];

                    return view('components.crud.partials.actions', compact('data', 'route', 'actions'))->render();
                })
                
                ->addColumn('show_skills', function ($data) {
                    $permissions = $data->permissions;
                    
                    return $permissions->map(function ($permission) {
                        return '<span
                                    class="inline-block px-2 py-1 bg-blue-100 text-blue-600 text-xs font-medium rounded-lg mr-1 mb-1">
                                    '. $permission->name .'
                                </span>';
                    })->implode(' '); 
                })

                ->rawColumns(['actions', 'show_skills'])
                ->make(true);
        }


        return DataTables::of($query)->make(true);
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
        $model = $allowed_models[$validated['model_type']]::findOrFail($validated['model_id']);

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



    public function show(Request $request, String $id){
        $role = Role::query()->with('permissions')->findOrFail($id);

        return response()->json(['message' => 'Role fetched successfully', 'success' => true, 'data' => $role], 200);
    }



    public function store(Request $request)
    {
        $space_id = get_space_id($request);

        $validated = $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'nullable|array', // Pastikan permissions berupa array
        ]);
    
        
        app(PermissionRegistrar::class)->setPermissionsTeamId($space_id);
        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'space',
        ]);
        // dd($request->all());

        
        // Tambahkan permissions ke role
        if ($request->has('permissions')) {
            $permissions = Permission::whereIn('id', $request->permissions)->pluck('name')->toArray();
            $role->syncPermissions($permissions, 'space');
        }
    
        return response()->json(['message' => 'Role created successfully', 'success' => true, 'data' => $role], 200);
    }



    public function update(Request $request, String $id)
    {
        $space_id = get_space_id($request);

        $request->validate([
            'name' => 'required|unique:roles,name,' . $id,
            'permissions' => 'array|exists:permissions,id', // Validate permission IDs
        ]);


        app(PermissionRegistrar::class)->setPermissionsTeamId($space_id);

        $role = Role::findOrFail($id);
        $role->update(['name' => $request->name]);

        // Convert permission IDs to names before syncing
        if ($request->has('permissions')) {
            $permissions = Permission::whereIn('id', $request->permissions)
                                        ->where('guard_name', 'space')
                                        ->get();
            $role->syncPermissions($permissions);
        }


        return response()->json(['message' => 'Role updated successfully', 'success' => true, 'data' => $role], 200);
    }



    public function destroy(String $id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return response()->json(['message' => 'Role deleted successfully', 'success' => true, 'data' => $role], 200);
    }


    public function index(Request $request){
        $space_id = get_space_id($request, false);

        $query = Permission::query();

        if($space_id){
            $query->where('guard_name', 'space');
        }

        $permissions = $query->get();


        return view('primary.access.roles.index', compact('permissions'));
    }
}