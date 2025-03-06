<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Store\StoreCustomer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class StoreCustomerController extends Controller
{
    public function index()
    {
        $store_id = Session::get('company_store_id');
        $store_customer = StoreCustomer::with('customer')->where('store_id', $store_id)->get();

        return view('store.customers.index', compact('store_customer'));
    }

    public function create()
    {
        return view('store.customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|integer',
            'status' => 'required|enum:active,inactive',
            'notes' => 'nullable|text'
        ]);

        $store_id = Session::get('company_store_id');
        $store_customer = StoreCustomer::create(array_merge($validated, ['store_id' => $store_id]));

        return redirect()->route('store_customers.index')->with('success', "Store Customer {$store_customer->customer->name} created successfully.");
    }

    public function show(string $id)
    {
        $store_customer = StoreCustomer::with('customer')->findOrFail($id);

        return view('store.customers.show', compact('store_customer'));
    }

    public function edit(string $id)
    {
        $store_customer = StoreCustomer::with('customer')->findOrFail($id);
        $sales_history = $store_customer->customer->sales()->get();
        

        return view('store.customers.edit', compact('store_customer', 'sales_history'));
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|integer',
            'status' => 'required|enum:active,inactive',
            'notes' => 'nullable|text'
        ]);

        $store_customer = StoreCustomer::all()->findOrFail($id);
        $store_customer->update($validated);

        return redirect()->route('store_customers.index')->with('success', "Store Customer {$store_customer->customer->name} updated successfully.");
    }

    public function destroy(string $id)
    {
        $store_customer = StoreCustomer::all()->findOrFail($id);
        $store_customer->delete();

        return redirect()->route('store_customers.index')->with('success', "Store Customer {$store_customer->customer->name} deleted successfully.");
    }
}
