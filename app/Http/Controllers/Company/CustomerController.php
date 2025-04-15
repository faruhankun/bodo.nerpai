<?php

namespace App\Http\Controllers\Company;
use App\Http\Controllers\Controller;

use App\Models\Company\Customer;
use Illuminate\Http\Request;

use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    /**
     * Display a listing of the customers.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customers = Customer::paginate(10);
        return view('company.customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new customer.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('company.customers.create');
    }

    /**
     * Store a newly created customer in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers',
            'phone_number' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
            'status' => 'required|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $customer = Customer::create($validatedData);
        return redirect()->route('customers.index')->with('success', 'Customer created successfully');
    }
    /**
     * Display the specified customer.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customer = Customer::find($id);
        return view('company.customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified customer.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $customer = Customer::find($id);
        return view('company.customers.edit', compact('customer'));
    }

    /**
     * Update the specified customer in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers,email,' . $id,
            'phone_number' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
            'status' => 'required|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $customer = Customer::find($id);
        $customer->update($validatedData);
        return redirect()->route('customers.index')->with('success', 'Customer updated successfully');
    }

    /**
     * Remove the specified customer from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $customer = Customer::find($id);
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully');
    }

    /**
     * Display a listing of the customer's sales.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function sales($id)
    {
        $customer = Customer::find($id);
        $sales = $customer->sales;
        return view('company.customers.sales', compact('customer', 'sales'));
    }


    public function getCustomersData(){
        $customers = Customer::query();
        return DataTables::of($customers)
            ->addColumn('actions', function ($customer) {
                return view('company.customers.partials.actions', compact('customer'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }



    // API
    public function search(Request $request)
    {
        $search = $request->q;

        $customers = Customer::where(function ($query) use ($search) {
            $query->where('name', 'like', "%$search%")
                ->orWhere('id', 'like', "%$search%");
        })
            ->orderBy('id', 'desc')
            ->limit(50) // limit hasil
            ->get()
            ->map(function ($customer) {
                return [
                    'id' => $customer->id,
                    'text' => "{$customer->id} - {$customer->name}",
                ];
            });

        return response()->json($customers);
    }

}
