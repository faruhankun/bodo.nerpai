<?php
namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;

// use App\Http\Controllers\Permission;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

use Illuminate\Support\Str;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class CompanyRoleController extends Controller
{
    public function index()
    {
        $company_roles = Role::with('permissions')->get();
        return view('company.company_roles.index', compact('company_roles'));
    }
    public function getRolesData(Request $request)
    {
        $roles = Role::with('permissions'); // Include permissions

        return DataTables::of($roles)
            ->addColumn('permissions', function ($role) {
                return $role->permissions->pluck('name')->implode(', '); // List permissions as a string
            })
            ->addColumn('actions', function ($role) {
                return view('roles.partials.actions', compact('role'))->render(); // Action buttons
            })
            ->filterColumn('permissions', function ($query, $keyword) {
                $query->whereHas('permissions', function ($query) use ($keyword) {
                    $query->where('name', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns(['actions']) // Allow HTML in actions column
            ->make(true);
    }
    public function create()
    {
        $permissions = Permission::all();

        $groupedPermissions = $permissions->groupBy(function ($permission) {
            $parts = explode(' ', $permission->name); // Pisahkan nama permission berdasarkan spasi
            return $parts[1] ?? 'others'; // Ambil kata kedua sebagai key, default 'others' jika tidak ada kata kedua
        });

        return view('company.company_roles.create', compact('groupedPermissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'array', // Pastikan permissions berupa array
        ]);
    
        $role = Role::create(['name' => $request->name]);
    
        // Tambahkan permissions ke role
        if ($request->has('permissions')) {
            $permissions = Permission::whereIn('id', $request->permissions)->pluck('name')->toArray();
            $role->syncPermissions($permissions);
        }
    
        return redirect()->route('company_roles.index')->with('success', 'Role created successfully!');
    }
    public function edit(string $id)
    {
        $permissions = Permission::all();

        $role = Role::with('permissions')->findOrFail($id);
        $groupedPermissions = $permissions->groupBy(function ($permission) {
            $parts = explode(' ', $permission->name); // Pisahkan nama permission berdasarkan spasi
            return $parts[1] ?? 'others'; // Ambil kata kedua sebagai key, default 'others' jika tidak ada kata kedua
        });

        return view('company.company_roles.edit', compact('role', 'groupedPermissions'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $id,
            'permissions' => 'array|exists:permissions,id', // Validate permission IDs
        ]);

        $role = Role::findOrFail($id);
        $role->update(['name' => $request->name]);

        // Convert permission IDs to names before syncing
        if ($request->has('permissions')) {
            $permissions = Permission::whereIn('id', $request->permissions)->pluck('name')->toArray();
            $role->syncPermissions($permissions);
        }

        return redirect()->route('company_roles.index')->with('success', 'Role updated successfully!');
    }

    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return redirect()->route('company_roles.index')->with('success', 'Role deleted successfully!');
    }
}