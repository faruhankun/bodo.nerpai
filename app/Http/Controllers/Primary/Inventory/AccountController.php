<?php

namespace App\Http\Controllers\Primary\Inventory;

use App\Http\Controllers\Controller;

use App\Models\Company\Finance\Account;
use App\Models\Company\Finance\AccountType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Carbon\Carbon;

use Yajra\DataTables\Facades\DataTables;

use App\Models\Primary\Inventory;
use App\Models\Primary\Space;
use App\Models\Primary\Transaction;

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
                // 'basecode' => 'required',
                'code' => 'required',
                'status' => 'required|string|max:50',
                'parent_id' => 'nullable',
                'notes' => 'nullable',
            ]);

            $requestData = $request->all();
            // $requestData['code'] = $request->input('basecode') . $request->input('code');
            
            $account = Inventory::findOrFail($id);
            $account->update($requestData);

            return response()->json([
                'status' => 'success',
                'message' => "Accounts {$account->name} updated successfully.",
                'data' => $account,
            ]);
            // return redirect()->route('accountsp.index')->with('success', "Accounts {$account->name} updated successfully.");
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
            // return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }



    public function destroy(String $id)
    {
        $account = Inventory::findOrFail($id);

        $account->delete();

        return redirect()->route('accountsp.index')->with('success', "Accounts {$account->name} deleted successfully.");
    }



    public function getAccountsData(){
        $space_id = session('space_id') ?? null;

        $accountsp = Inventory::with('type', 'parent', 'tx_details')
                                ->where('model_type', 'ACC');

        if($space_id){
            $space = Space::findOrFail($space_id);

            $spaceIds = $space->AllChildren()->pluck('id')->toArray();
            $spaceIds = array_merge($spaceIds, [$space_id]);

            $accountsp = $accountsp->where('space_type', 'SPACE')
                                    ->whereIn('space_id', $spaceIds);
        } else {
            $accountsp->whereRaw('1 = 0');
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



    // Summaries
    public $summary_types = [
        'balance_sheet' => 'Neraca',
        'cashflow' => 'Cashflow',
        'profit_loss' => 'Laba Rugi',
    ];

    public function summary(Request $request)
    {
        $space_id = session('space_id') ?? null;
        if(is_null($space_id)){
            abort(403);
        }

        $space = Space::findOrFail($space_id);
        $spaces = $space->spaceAndChildren();



        // generate data by date
        $validated = $request->validate([
            'summary_type' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $start_date = $validated['start_date'] ?? null;
        $end_date = $validated['end_date'] ?? now()->format('Y-m-d');

        $end_time = Carbon::parse($end_date)->endOfDay();
        
        $txs = Transaction::with('input', 'type', 'details', 'details.detail') 
                            ->where('model_type', 'JE')
                            ->where('space_type', 'SPACE')
                            ->whereIn('space_id', $spaces->pluck('id')->toArray())
                            ->where('sent_time', '<=', $end_time)
                            ->orderBy('sent_time', 'asc');

        if(!is_null($start_date)){
            $start_time = Carbon::parse($start_date)->startOfDay();
            $txs = $txs->where('sent_time', '>=', $start_time);
        }
        
        $txs = $txs->get();


        // generate data by account
        $data = collect();
        $data->summary_types = $this->summary_types;
        $data->account = Inventory::with('type', 'parent', 'tx_details')
                                ->where('model_type', 'ACC')
                                ->where('space_type', 'SPACE')
                                ->whereIn('space_id', $spaces->pluck('id')
                                ->toArray())
                                ->get();
        $data = $this->getSummaryData($data, $txs, $spaces, $validated);

        return view('primary.inventory.accountsp.summary', compact('data', 'txs', 'spaces'));
    }


    public function getSummaryData($data, $txs, $spaces, $validated){
        $account = [];
        foreach($txs as $tx){
            foreach($tx->details as $detail){
                $acc = $detail->detail;

                if(!isset($account[$acc->id])){
                    $account[$acc->id] = array_merge($acc->toArray(),
                        [
                            'type' => $acc->type,
                            'debit' => 0,
                            'credit' => 0,
                            'details' => [],
                        ]
                    );
                }

                $account[$acc->id]['debit'] += $detail->debit;
                $account[$acc->id]['credit'] += $detail->credit;

                $tx_data = $tx->toArray();
                $detail->tx = $tx_data;
                $account[$acc->id]['details'][] = $detail;
            }
        }

        foreach($account as $key => $acc){
            $account[$key]['balance'] = ($account[$key]['debit'] - $account[$key]['credit']) * $acc['type']->debit;
        }
        $data->account = collect($account);

        $data->profit_loss = $this->calculate_profit_loss($data->account);

        return $data;
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
}
