<?php
namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;

use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyPermissionController extends Controller
{
    // Menampilkan daftar permission
    public function index()
    {
        $company_permissions = Permission::all();
        return view('company.company_permissions.index', compact('company_permissions'));
    }

    // Menampilkan form untuk membuat permission baru
    public function create()
    {
        return view('company.company_permissions.create');
    }

    // Menyimpan permission baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:company_permissions,name',
        ]);

        Permission::create(['name' => $request->name]);

        return redirect()->route('company_permissions.index')->with('success', 'Permission created successfully!');
    }

    // Menampilkan form untuk mengedit permission
    public function edit(string $id)
    {
        $permission = Permission::findOrFail($id);
        return view('company.company_permissions.edit', compact('permission'));
    }

    // Mengupdate permission
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name,' . $id,
        ]);

        $permission = Permission::findOrFail($id);
        
        $permission->update(['name' => $request->name]);

        return redirect()->route('company_permissions.index')->with('success', 'Permission updated successfully!');
    }

    // Menghapus permission
    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('company_permissions.index')->with('success', 'Permission deleted successfully!');
    }
}