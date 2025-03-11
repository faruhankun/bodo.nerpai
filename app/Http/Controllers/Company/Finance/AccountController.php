<?php

namespace App\Http\Controllers\Company\Finance;

use App\Http\Controllers\Controller;
use App\Models\Company\Finance\Account;
use App\Models\Company\Finance\AccountType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

enum Status: string
{
    case Active = 'Active';
    case Inactive = 'Inactive';
}

class AccountController extends Controller
{
    public function index()
    {
        $accounts = Account::all();
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
}
