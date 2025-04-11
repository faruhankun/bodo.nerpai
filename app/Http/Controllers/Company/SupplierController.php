<?php

namespace App\Http\Controllers\Company;

use App\Models\Company\Supplier;
use App\Services\SupplierService;
use App\Http\Requests\Supplier\StoreSupplierRequest;
use App\Http\Requests\Supplier\UpdateSupplierRequest;

use App\Models\Space\Person;
use App\Models\Space\Company;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

use Yajra\DataTables\Facades\DataTables;


class SupplierController extends Controller
{
    protected Supplier $supplier;
    protected SupplierService $supplierService;

    public function __construct(Supplier $supplier, SupplierService $supplierService)
    {
        $this->supplier = $supplier;
        $this->supplierService = $supplierService;
    }

    public function index()
    {
        $suppliers = $this->supplierService->getAll();

        return view('company.suppliers.index', compact('suppliers'));
    }

    public function show(String $id)
    {
        $supplier = $this->supplierService->getOne($id);
        return view('company.suppliers.show', compact('supplier'));
    }

    public function create()
    {
        return view('company.suppliers.create');
    }

    public function store(StoreSupplierRequest $request)
    {
        $this->supplierService->store($request->validated());
        return redirect()->route('suppliers.index')->with('success', 'Supplier created successfully.');
    }

    public function edit(String $id)
    {
        $supplier = $this->supplierService->getOne($id);

        $company_id = session('company_id');

        $persons = Person::all();
        // company who's not supplier and who's not this comapny itself
        $used_companies_ids = Supplier::whereNotNull('entity_id')
                                    ->where('entity_type', 'COMP')
                                    ->where('entity_id', '!=', $supplier->entity_id)
                                    ->pluck('entity_id');

        //dd($used_companies);

        $companies = Company::whereNotIn('id', $used_companies_ids)
                                ->where('id', '!=', $company_id)
                                ->get();

        return view('company.suppliers.edit', compact('supplier', 'persons', 'companies'));
    }

    public function update(UpdateSupplierRequest $request, String $id)
    {
        $this->supplierService->update($request->validated(), $id);
        return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully.');
    }

    public function destroy(String $id)
    {
        $this->supplierService->destroy($id);
        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted successfully.');
    }


    public function getSuppliersData(){
        $suppliers = Supplier::query();
        return DataTables::of($suppliers)
            ->addColumn('actions', function ($supplier) {
                return view('company.suppliers.partials.actions', compact('supplier'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }
}
