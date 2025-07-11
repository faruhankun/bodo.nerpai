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
        $space_id = get_space_id($request);

        $query = Role::query()
                    ->with('permissions')
                    ->where('team_id', $space_id);


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



        // return result
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
            'permissions' => 'array', // Pastikan permissions berupa array
        ]);
    
        
        app(PermissionRegistrar::class)->setPermissionsTeamId($space_id);
        $role = Role::create(['name' => $request->name]);
        // dd($request->all());

        
        // Tambahkan permissions ke role
        if ($request->has('permissions')) {
            $permissions = Permission::whereIn('id', $request->permissions)->pluck('name')->toArray();
            $role->syncPermissions($permissions);
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
            $permissions = Permission::whereIn('id', $request->permissions)->pluck('name')->toArray();
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
}