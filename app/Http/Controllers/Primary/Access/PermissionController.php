<?php
namespace App\Http\Controllers\Primary\Access;

use App\Http\Controllers\Controller;

use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;




class PermissionController extends Controller
{
    public function getData(Request $request){
        $space_id = get_space_id($request, false);

        $query = Permission::query();


        // guard
        $guard_name = $request->get('guard_name') ?? 'space';
        if($guard_name){
            if($guard_name == 'space'){
                $query->where('name', 'like', 'space%');
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
                ->orWhere('id', 'like', "%{$keyword}%");
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
                    $route = 'skills';
                    
                    $actions = [
                        'edit' => 'modal',
                        // 'delete' => 'button',
                    ];

                    return view('components.crud.partials.actions', compact('data', 'route', 'actions'))->render();
                })
                ->rawColumns(['actions'])
                ->make(true);
        }



        // return result
        return DataTables::of($query)->make(true);
    }



    public function index(Request $request)
    {
        return view('primary.access.skills.index');
    }



    public function show(Request $request, String $id){
        try {
            $permission = Permission::findOrFail($id);

            return response()->json(['message' => 'Permission fetched successfully', 'success' => true, 'data' => $permission], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'success' => false], 500);
        }
    }


    // Menyimpan permission baru
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|unique:permissions,name',
                'guard_name' => 'nullable',
            ]);

            $validated['guard_name'] = 'web';

            
            $data = Permission::create($validated);

            return response()->json(['message' => 'Permission created successfully', 'success' => true, 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'success' => false], 500);
        }
    }



    // Mengupdate permission
    public function update(Request $request, String $id)
    {
        try {
            $request->validate([
                'name' => 'required|unique:permissions,name,' . $id,
            ]);

            $permission = Permission::findOrFail($id);
            $permission->update(['name' => $request->name, 'guard_name' => 'web']);

            return response()->json(['message' => 'Permission updated successfully', 'success' => true, 'data' => $permission], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'success' => false], 500);
        }
    }



    // Menghapus permission
    public function destroy(Request $request, $id)
    {
        try {
            $permission = Permission::findOrFail($id);
            dd($permission);

            $permission->delete();

            return response()->json(['message' => 'Permission deleted successfully', 'success' => true, 'data' => $permission], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'success' => false], 500);
        }
    }
}