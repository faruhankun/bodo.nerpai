<?php

namespace App\Http\Controllers\Primary\Inventory;

use App\Http\Controllers\Controller;

use App\Models\Company\Finance\Account;
use App\Models\Company\Finance\AccountType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

use Yajra\DataTables\Facades\DataTables;

use App\Models\Primary\Inventory;
use App\Models\Primary\Space;

enum Status: string
{
    case Active = 'Active';
    case Inactive = 'Inactive';
}

class AccountController extends Controller
{
    public function index()
    {
        $accounts = Inventory::where('model_type', 'ACC')->get();
        
        $space_id = session('space_id') ?? null;
        if($space_id){
            $space = Space::findOrFail($space_id);
            $spaceIds = $space->allParents()->pluck('id')->toArray();
            $spaceIds = array_merge($spaceIds, [$space_id]);

            $accounts = $accounts->whereIn('space_id', $spaceIds);
        }

        $account_types = AccountType::all();
        return view('primary.inventory.accountsp.index', compact('account_types', 'accounts'));
    }

    public function store(Request $request)
    {
        $space_id = session('space_id') ?? null;

        try {
            $request->validate([
                'name' => 'required',
                'type_id' => 'required',
                'basecode' => 'required',
                'code' => 'required',
                'status' => 'required|string|max:50',
                'parent_id' => 'nullable',
                'notes' => 'nullable',
            ]);

            $requestData = $request->all();
            $requestData['code'] = $request->input('basecode') . $request->input('code');

            if($space_id){
                $requestData['space_type'] = 'SPACE';
                $requestData['space_id'] = $space_id;
            }

            $requestData += [
                'model_type' => 'ACC',
                'type_type' => 'ACCT',
                'parent_type' => 'IVT',
            ];

            $account = Inventory::create($requestData);

            return redirect()->route('accountsp.index')->with('success', "Accounts {$account->name} created successfully.");
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function update(Request $request, String $id)
    {
        try {
            $request->validate([
                'name' => 'required',
                'type_id' => 'required',
                'basecode' => 'required',
                'code' => 'required',
                'status' => 'required|string|max:50',
                'parent_id' => 'nullable',
                'notes' => 'nullable',
            ]);

            $requestData = $request->all();
            $requestData['code'] = $request->input('basecode') . $request->input('code');
            
            $account = Inventory::findOrFail($id);
            $account->update($requestData);

            return redirect()->route('accountsp.index')->with('success', "Accounts {$account->name} updated successfully.");
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function destroy(String $id)
    {
        $account = Inventory::findOrFail($id);

        $account->delete();

        return redirect()->route('accountsp.index')->with('success', "Accounts {$account->name} deleted successfully.");
    }



    public function getAccountsData(){
        $accountsp = [];

        $space_id = session('space_id') ?? null;

        $accountsp = Inventory::with('type', 'parent', 'tx_details')->where('model_type', 'ACC')->get();

        if($space_id){
            $space = Space::findOrFail($space_id);

            $spaceIds = $space->AllChildren()->pluck('id')->toArray();
            $spaceIds = array_merge($spaceIds, [$space_id]);

            $accountsp = $accountsp->where('space_type', 'SPACE')
                                    ->whereIn('space_id', $spaceIds);
        }

        return DataTables::of($accountsp)
            ->addColumn('getAccountBalance', function ($data) {
                return $data->getAccountBalance();
            })
            ->addColumn('actions', function ($data) {
                $route = 'accountsp';
                
                $actions = [
                    'edit' => 'modal',
                    'delete' => 'button',
                ];

                return view('components.crud.partials.actions', compact('data', 'route', 'actions'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }
}
