<?php
namespace App\Http\Controllers\Space;

use App\Http\Controllers\Controller;

use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    // Menampilkan daftar permission
    public function index()
    {
        $permissions = Permission::all();
        return view('space.permissions.index', compact('permissions'));
    }

    // Menampilkan form untuk membuat permission baru
    public function create()
    {
        return view('space.permissions.create');
    }

    // Menyimpan permission baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name',
        ]);

        Permission::create(['name' => $request->name]);

        return redirect()->route('permissions.index')->with('success', 'Permission created successfully!');
    }

    // Menampilkan form untuk mengedit permission
    public function edit(Permission $permission)
    {
        return view('space.permissions.edit', compact('permission'));
    }

    // Mengupdate permission
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name,' . $permission->id,
        ]);

        $permission->update(['name' => $request->name]);

        return redirect()->route('permissions.index')->with('success', 'Permission updated successfully!');
    }

    // Menghapus permission
    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('permissions.index')->with('success', 'Permission deleted successfully!');
    }
}