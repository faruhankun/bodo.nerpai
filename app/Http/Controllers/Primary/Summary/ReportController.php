<?php

namespace App\Http\Controllers\Primary\Summary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Company\Finance\Account;

use App\Models\Primary\Space;
use App\Models\Primary\Inventory;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('primary.reports.index');
    }


    

    public function show(string $id)
    {
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

        foreach($accountsp as $account){
            $account->balance = $account->getAccountBalance();
        }

        switch($id){
            case 'profit-and-loss':
                $accounts = $accountsp;
                return view('primary.reports.finance.profit-and-loss', compact('id', 'accounts'));
            case 'cashflow':
                $accounts = $accountsp;
                return view('primary.reports.finance.cashflow', compact('id', 'accounts'));
            case 'balance-sheet':
                $accounts = $accountsp;
                return view('primary.reports.finance.balance-sheet', compact('id', 'accounts'));
            default:
                ;
        }

        return view('primary.reports.index');
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
        //
    }
}
