<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Artisan;

use App\Models\Company\Store;
use App\Models\Employee;
use App\Models\Store\StoreEmployee;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employeeId = session('employee')?->id;
        $employee = Employee::with('store')->find($employeeId);
        $stores = $employee->store;

        return view('company.stores.index', compact('stores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            // 'address' => 'nullable|string|max:255',
        ]);

        $store = Store::updateOrCreate(
            ['code' => $validated['code']],
            $validated
        );

        $this->setupNewStore($store);

        return redirect()->route('stores.index')->with('success', "Store {$store->name} created successfully.");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $store = Store::findOrFail($id);
        $employee_id = session('employee')->id;

        // is authorized to delete store?
        $store_employee = StoreEmployee::where('store_id', $store->id)
            ->where('employee_id', $employee_id)
            ->first();

        if (!$store_employee) {
            return redirect()->route('stores.index')->with('error', 'Anda tidak memiliki akses ke store ini !');
        }

        if($store_employee->store_role_id != '1'){
            return redirect()->route('stores.index')->with('error', 'Anda tidak memiliki akses hapus store ini !');
        }

        // force delete store
        $store->forceDelete();

        return redirect()->route('stores.index')->with('success', "Store {$store->name} deleted successfully.");
    }

    public function switchStore(Request $request, $id)
    {
        $this->forgetStore();
        
        $store = Store::findOrFail($id);

        Session::put('company_store_id', $store->id);
        Session::put('company_store_code', $store->code);
        Session::put('company_store_name', $store->name);
        Session::put('layout', 'store');

        return redirect()->route('dashboard-store')->with('success', "Anda masuk ke {$store->name}");
    }


    public function exitStore(Request $request, $route = 'dashboard-company')
    {
        $this->forgetStore();

        // Redirect ke halaman (atau dashboard company)
        // change layout to company
        Session::put('layout', 'company');

        return redirect()->route($route)->with('success', 'Anda telah keluar dari store!');
    }

    public function forgetStore()
    {
        foreach(session()->all() as $key => $value) {
            if(str_contains($key, 'store')) {
                session()->forget($key);				
            }
        }
    }


    public function seedDatabase($database, $seeder_class)
	{
		Artisan::call('db:seed', [
			'--database' => $database,
			'--class' => $seeder_class, // Sesuaikan dengan seeder untuk perusahaan
			'--force' => true
		]);
	}


    public function setupNewStore($store)
    {
        $employee_id = session('employee')->id;

        // seed store
        $this->seedDatabase('tenant', 'StoreSeeder');

        // berikan akses employee sbg store manager
        $store_employee = StoreEmployee::create([
            'store_id' => $store->id,
            'employee_id' => $employee_id,
            'store_role_id' => '1',             // assume its store manager
            'status' => 'active',
        ]);
    }
}
