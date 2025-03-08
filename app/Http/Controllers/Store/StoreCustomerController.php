<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Company\Customer;
use App\Models\Store\StoreCustomer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules\Enum;

enum Status: string
{
    case Active = 'Active';
    case Inactive = 'Inactive';
}

class StoreCustomerController extends Controller
{
    public function index()
    {
        $store_id = Session::get('company_store_id');
        $store_customers = StoreCustomer::with('customer')->where('store_id', $store_id)->get();

        $customers = Customer::where('status', 'Active')
            ->whereDoesntHave('storeCustomers', function ($query) use ($store_id) {
                $query->where('store_id', $store_id);
            })->get();

        return view('store.store_customers.index', compact('store_customers', 'customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|integer',
            'status' => ['required', new Enum(Status::class)],
            'notes' => 'nullable|string'
        ]);

        $store_id = Session::get('company_store_id');
        $store_customer = StoreCustomer::create(array_merge($validated, ['store_id' => $store_id]));

        return redirect()->route('store_customers.index')->with('success', "Store Customer {$store_customer->customer->name} created successfully.");
    }

    public function show(string $id)
    {
        $store_customer = StoreCustomer::with('customer')->findOrFail($id);

        return view('store.store_customers.show', compact('store_customer'));
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|integer',
            'status' => ['required', new Enum(Status::class)],
            'notes' => 'nullable|string'
        ]);

        $store_customer = StoreCustomer::all()->findOrFail($id);
        $store_customer->update($validated);

        return redirect()->route('store_customers.show', $store_customer->id)->with('success', "Store Customer {$store_customer->customer->name} updated successfully.");
    }

    public function destroy(string $id)
    {
        $store_customer = StoreCustomer::all()->findOrFail($id);
        $store_customer->delete();

        return redirect()->route('store_customers.index')->with('success', "Store Customer {$store_customer->customer->name} deleted successfully.");
    }
}
