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


    

    public function show(Request $request, $id)
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

        $start_date = $request->input('start_date') ?? null;
        $end_date = $request->input('end_date') ?? null;
        foreach($accountsp as $account){
            $account->balance = $account->getAccountBalance($start_date, $end_date);
        }

        $profit_loss = [];
        $param = $request->all();
        switch($id){
            case 'profit-and-loss':
                $accounts = $accountsp;
                $data = $this->calculate_profit_loss($accounts);
                return view('primary.reports.finance.profit-and-loss', compact('id', 'accounts', 'param', 
                            'data'));
            case 'cashflow':
                $accounts = $accountsp;
                return view('primary.reports.finance.cashflow', compact('id', 'accounts'));
            case 'balance-sheet':
                $accounts = $accountsp;
                $data = $this->calculate_balance_sheet($accounts);
                return view('primary.reports.finance.balance-sheet', compact('id', 'accounts', 'param', 'data'));
            default:
                ;
        }

        return view('primary.reports.index');
    }

    
    public function calculate_profit_loss($accounts){
        $data = [];
        $data['pendapatan'] = $accounts->where('type_id', 12);
        $data['beban_pokok'] = $accounts->where('type_id', 13);
        $data['biaya_operasional'] = $accounts->where('type_id', 14);
        $data['pendapatan_lainnya'] = $accounts->where('type_id', 15);
        $data['beban_lainnya'] = $accounts->where('type_id', 16);

        $data['total_pendapatan'] = $data['pendapatan']->sum('balance');
        $data['total_beban_pokok'] = $data['beban_pokok']->sum('balance');
        $data['total_biaya_operasional'] = $data['biaya_operasional']->sum('balance');
        $data['total_pendapatan_lainnya'] = $data['pendapatan_lainnya']->sum('balance');
        $data['total_beban_lainnya'] = $data['beban_lainnya']->sum('balance');

        $data['laba_kotor'] = $data['total_pendapatan'] - $data['total_beban_pokok'];
        $data['laba_operasional'] = $data['laba_kotor'] - $data['total_biaya_operasional'];
        $data['laba_bersih'] = $data['laba_operasional'] + $data['total_pendapatan_lainnya'] - $data['total_beban_lainnya'];

        return $data;
    }

    public function calculate_balance_sheet($accounts){
        $data = [];
    
        $data['assets'] = $accounts->whereBetween('type_id', [1, 7]);
        $data['liabilities'] = $accounts->whereBetween('type_id', [8, 10]);
        $data['equities'] = $accounts->where('type_id', 11);

        $data['pnl'] = $this->calculate_profit_loss($accounts);
        // $data['pnl_before'] = $data['pnl'];

        $data['totalAssets'] = $data['assets']->sum('balance');
        $data['totalLiabilities'] = $data['liabilities']->sum('balance');
        $data['totalEquities'] = $data['equities']->sum('balance') 
                                    // + $data['pnl_before']['laba_bersih']
                                    + $data['pnl']['laba_bersih'];
    
        return $data;
    }
}
