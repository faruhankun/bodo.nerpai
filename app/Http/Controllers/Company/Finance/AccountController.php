<?php

namespace App\Http\Controllers\Company\Finance;

use App\Http\Controllers\Controller;
use App\Models\Company\Finance\Account;
use App\Models\Company\Finance\AccountType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

use Yajra\DataTables\Facades\DataTables;


enum Status: string
{
    case Active = 'Active';
    case Inactive = 'Inactive';
}

class AccountController extends Controller
{
    public function index()
    {
        $accounts = Account::with('account_type', 'parent', 'children')->orderBy('code')->get();
        $account_types = AccountType::all();
        return view('company.finance.accounts.index', compact("accounts", "account_types"));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'type_id' => 'required',
                'basecode' => 'required',
                'code' => 'required',
                'status' => ['required', new Enum(Status::class)],
                'parent_id' => 'nullable',
                'notes' => 'nullable',
            ]);

            $requestData = $request->all();
            $requestData['code'] = $request->input('basecode') . $request->input('code');

            $account = Account::create($requestData);

            return redirect()->route('accounts.index')->with('success', "Accounts {$account->name} created successfully.");
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
                'status' => ['required', new Enum(Status::class)],
                'parent_id' => 'nullable',
                'notes' => 'nullable',
            ]);

            $requestData = $request->all();
            $requestData['code'] = $request->input('basecode') . $request->input('code');
            
            $account = Account::findOrFail($id);
            $account->update($requestData);

            return redirect()->route('accounts.index')->with('success', "Accounts {$account->name} updated successfully.");
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function destroy(String $id)
    {
        $account = Account::findOrFail($id);
        $account->delete();
        return redirect()->route('accounts.index')->with('success', "Accounts {$account->name} deleted successfully.");
    }


    public function getAccountsData(){
        $accounts = Account::with('account_type', 'parent', 'children')->orderBy('code')->get();
        $account_types = AccountType::all();

        return DataTables::of($accounts)
            ->addColumn('actions', function ($data) {
                $route = 'accounts';

                $actions = [
                    'edit' => 'modal',
                    'edit_modal' => 'company.finance.accounts.edit2',
                    'delete' => 'button',
                ];

                return view('components.crud.partials.actions', compact('data', 'route', 'actions'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }
}
