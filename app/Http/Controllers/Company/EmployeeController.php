<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use App\Models\CompanyUser;

use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with('companyuser', 'role')
                    ->where('status', 'active')
                    ->get();
        return view('company.employees.index', compact('employees'));
    }
    public function create()
    {
        $users = CompanyUser::whereDoesntHave('employee')
            ->where('status', 'approved')
            ->get(); // List all users which not employees
        $roles = \Spatie\Permission\Models\Role::all(); // List all roles
        return view('company.employees.create', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:company_users,id',
            'reg_date' => 'required|date',
            'role_id' => 'required|exists:roles,id',
        ]);

        Employee::firstOrCreate([
            'company_user_id' => $request->user_id,
            'reg_date' => $request->reg_date,
            'status' => 'active', // Default status is active
            'role_id' => $request->role_id,
        ]);

        // Upgrade guest to employee
        $companyUser = CompanyUser::find($request->user_id);
        if($companyUser->user_type == 'guest'){
            $companyUser->update(['user_type' => 'employee']);
        }

        return redirect()->route('employees.index')->with('success', 'Employee assigned successfully!');
    }
    
    public function edit(string $id)
    {
        $employee = Employee::find($id);
        $roles = \Spatie\Permission\Models\Role::all(); // List all roles
        return view('company.employees.edit', compact('employee', 'roles'));
    }
    
    public function update(Request $request, string $id)
    {
        $request->validate([
            'reg_date' => 'required|date',
            'out_date' => 'nullable|date|after_or_equal:reg_date',
            'role_id' => 'required|exists:roles,id',
        ]);

        $employee = Employee::with('companyuser')->find($id);

        $employee->update([
            'reg_date' => $request->reg_date,
            'out_date' => $request->out_date,
            'status' => $request->out_date ? 'inactive' : 'active', // Automatically update status
            'role_id' => $request->role_id,
        ]);

        return redirect()->route('employees.index')->with('success', "Employee {$employee->companyuser->user->name} updated successfully!");
    }
    public function destroy(string $id)
    {
        $employee = Employee::findOrFail($id);

        // company user jadi guest
        if($employee->companyuser){
            $employee->companyuser->update(['user_type' => 'guest']);
            $employee->update(['company_user_id' => null]);
        }

        // notify user


        // soft-delete
        $employee->out_date = date('Y-m-d');
        $employee->status = 'inactive';
        $employee->deleted_at = now();
        $employee->save();

        return redirect()->route('employees.index')->with('success', 'Employee deactivated successfully!');
    }

    
}
