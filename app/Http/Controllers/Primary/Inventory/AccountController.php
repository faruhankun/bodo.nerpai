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
use App\Models\Primary\TransactionDetail;

use App\Services\Primary\Basic\EximService;



enum Status: string
{
    case Active = 'Active';
    case Inactive = 'Inactive';
}

class AccountController extends Controller
{
    protected $eximService;

    protected $import_columns = [
        'account_code', 
        'account_name',
        'type_id', 
        'type_name', 
        'parent_code',
        'parent_name',
        'notes',
    ];


    public function search(Request $request){
        $query = $this->getQueryData($request);

        $keyword = $request->get('q');
        if($keyword){
            $query->where(function($q) use ($keyword){
                $q->where('code', 'like', "%{$keyword}%")
                ->orWhere('name', 'like', "%{$keyword}%")
                ->orWhere('notes', 'like', "%{$keyword}%")
                ->orWhereHas('type', function ($q2) use ($keyword) {
                    $q2->where('name', 'like', "%{$keyword}%");
                });
            });
        }


        $limit = $request->get('limit');
        if($limit){
            if($limit != 'all'){
                $query->limit($limit);
            }
        } else {
            $query->limit(50);
        }

        $query->orderBy('code', 'asc');

        // filter parent or children only
        $parent_only = $request->get('parent_only');
        if($parent_only){
            $query->whereNull('parent_id');
        }

        $has_no_children = $request->get('has_no_children');
        if($has_no_children){
            $query->whereDoesntHave('children');
        }

        return DataTables::of($query)
            ->addColumn('has_children', function ($data) {
                return $data->children->count() > 0 ? true : false;
            })
            ->addColumn('children_count', function ($data) {
                return $data->children->count();
            })
            ->make(true);
    }



    public function show($id){
        try {
            $account = Inventory::with('type', 'parent', 'children')->findOrFail($id);
        } catch (\Throwable $th) {
            return response()->json([
                'data' => [],
                'recordsFiltered' => 0,
                'error' => $th->getMessage(),
            ]);
        }

        return response()->json([
            'data' => array($account),
            'recordsFiltered' => 1,
        ]);
    }



    public function __construct(EximService $eximService)
    {
        $this->eximService = $eximService;
    }


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
        $space_id = get_space_id($request);
        $request_type = $request->input('request_type') ?? 'api';

        try {
            $request->validate([
                'name' => 'required',
                'type_id' => 'required',
                // 'basecode' => 'nullable',
                'code' => 'required',
                'status' => 'nullable|string|max:50',
                'parent_id' => 'nullable',
                'notes' => 'nullable',
            ]);

            $requestData = $request->all();

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

            if($request_type == 'api'){
                return response()->json([
                    'success' => true,
                    'message' => "Accounts {$account->name} created successfully.",
                    'data' => $account,
                ]);
            }

            return redirect()->route('accountsp.index')->with('success', "Accounts {$account->name} created successfully.");
        } catch (\Exception $e) {
            if($request_type == 'api'){
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ]);
            }

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
                'status' => 'nullable|string|max:50',
                'parent_id' => 'nullable',
                'notes' => 'nullable',
            ]);

            $requestData = $request->all();
            // $requestData['code'] = $request->input('basecode') . $request->input('code');
            
            $account = Inventory::findOrFail($id);
            $account->update($requestData);

            return response()->json([
                'success' => true,
                'message' => "Accounts {$account->name} updated successfully.",
                'data' => $account,
            ]);
            // return redirect()->route('accountsp.index')->with('success', "Accounts {$account->name} updated successfully.");
        } catch (\Exception $e) {
            return response()->json([
                'input' => $request->all(),
                'error' => $e->getMessage(),
                'message' => $e->getMessage(),
            ], 500);
            // return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }



    public function destroy(Request $request, String $id)
    {
        $account = Inventory::with('tx_details')->findOrFail($id);

        // check apakah ada transaksi
        if ($account->tx_details->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Account has related transaction. Cannot delete.',
            ], 500);
        }


        $account->delete();

        $request_source = $request->input('request_source') ?? 'api';
        if($request_source == 'api'){
            return response()->json([
                'success' => true,
                'message' => "Accounts {$account->name} deleted successfully.",
                'data' => $account,
            ]);
        }

        return redirect()->route('accountsp.index')->with('success', "Accounts {$account->name} deleted successfully.");
    }



    public function getAccountsData(Request $request){
        // if q is not null, then search
        if($request->has('q')) {
            return $this->search($request);
        }

        $accountsp = $this->getQueryData($request);

        return DataTables::of($accountsp)
            ->addColumn('getAccountBalance', function ($data) {
                // return $data->getAccountBalance();
                return 0;
            })
            ->addColumn('data', function ($data) {
                return $data;
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


    public function getAccountTypesData(Request $request){
        $account_types = AccountType::all();

        return DataTables::of($account_types)
            ->make(true);
    }


    public function getQueryData(Request $request){
        $space_id = get_space_id($request);

        $query = Inventory::with('type', 'parent', 'children', 'children.type')
                            ->where('model_type', 'ACC')
                            ->where('space_type', 'SPACE')
                            ->where('space_id', $space_id);

        return $query;
    }



    // Export Import
    public function importTemplate(){
        $response = $this->eximService->exportCSV(['filename' => 'accountsp_import_template.csv'], $this->import_columns);

        return $response;
    }

    public function exportData(Request $request)
    {
        $params = json_decode($request->get('params'), true);
        
        $query = $this->getQueryData($request);
        // search & order filter
        $query = $this->eximService->exportQuery($query, $params, ['code', 'name', 'type_id', 'notes']);

        $query->take(10000);
        $collects = $query->get();


        // Prepare the CSV data
        $filename = 'export_accountsp_' . now()->format('Ymd_His') . '.csv';
        $data = collect();

        // fetch transation into array
        // grouped by number
        foreach($collects as $collect){
            $row = [];

            $row['account_code'] = $collect->code;
            $row['account_name'] = $collect->name;
            
            $row['type_id'] = $collect->type_id;
            $row['type_name'] = $collect->type->name ?? '';

            $row['parent_code'] = $collect->parent->code ?? '';
            $row['parent_name'] = $collect->parent->name ?? '';
            
            $row['status'] = $collect->status;
            $row['notes'] = $collect->notes;

            $data[] = $row;
        }

        $response = $this->eximService->exportCSV(['filename' => $filename], $data);

        return $response;
    }

    public function importData(Request $request)
    {
        $space_id = $request->space_id ?? (session('space_id') ?? null);
        $player_id = $request->player_id ?? (session('player_id') ?? auth()->user()->player->id);

        try {
            $validated = $request->validate([
                'file' => 'required|mimes:csv,txt'
            ]);

            $file = $validated['file'];
            $data = collect();
            $failedRows = collect();
            $requiredHeaders = ['account_code', 'account_name'];

            // Read the CSV into an array of associative rows
            $data = $this->eximService->convertCSVtoArray($file, ['requiredHeaders' => $requiredHeaders]);

            
            $accountsp = $this->getQueryData($request)->get();
            $account_types = AccountType::all();
            
            // process data
            foreach($data as $i => $row){
                try {
                    $account_data = [
                        'code' => $row['account_code'],
                        'name' => $row['account_name'],
                        'notes' => $row['notes'] ?? null,

                        'model_type' => 'ACC',
                        'type_type' => 'ACCT',
                        'parent_type' => 'IVT',
                        'space_type' => 'SPACE',
                        'space_id' => $space_id,
                    ];
                    // skip if no code or name
                    // if (empty($row['type_id']) && empty($row['type_name'])) {
                    //     throw new \Exception('Missing required field: type_id or type_name');
                    // }


                    // look for type
                    if(!empty($row['type_name'])){
                        $type = $account_types->where('name', $row['type_name'])->first();

                        if(!$type){
                            if(empty($row['type_id'])){
                                $type = $account_types->where('id', '1')->first();
                            }
                        } 

                        $row['type_id'] = $type->id;
                    }
                    if($row['type_id']){
                        $account_data['type_id'] = $row['type_id'];
                    } else {
                        $account_data['type_id'] = 1;
                    }



                    // look for parent
                    if(!empty($row['parent_code']) || !empty($row['parent_name'])){
                        $parent = $accountsp->filter(function ($account) use ($row) {
                            return $account->code == $row['parent_code'] || $account->name == $row['parent_name'];
                        })->first();

                        // create or use parent
                        if(!$parent){
                            $parent = Account::create([
                                'code' => $row['parent_code'],
                                'name' => $row['parent_name'],
                                'notes' => $row['notes'] ?? null,
                            ]);
    
                            $accountsp->push($parent);
                        }

                        $account_data['parent_id'] = $parent->id;
                    }



                    // look for account
                    $account = $accountsp->filter(function ($account) use ($row) {
                        return $account->code == $row['account_code'] && $account->name == $row['account_name'];
                    })->first();
                    

                    if ($account) {
                        // update ke DB
                        $account->update($account_data);

                        // update juga ke Collection manual (kalau perlu)
                        $index = $accountsp->search(function ($acc) use ($account) {
                            return $acc->id === $account->id;
                        });

                        if ($index !== false) {
                            $accountsp->put($index, $account); // replace data di Collection dengan yang baru
                        }

                    } else {
                        // kalau tidak ketemu, create
                        $account = Inventory::create($account_data);
                        $accountsp->push($account); // tambahkan ke Collection
                    }
                } catch (\Throwable $e) {
                    $row['row'] = $i + 2; 
                    $row['error'] = $e->getMessage();
                    $failedRows[] = $row;
                }
            }


            // Jika ada row yang gagal, langsung return CSV dari memory
            if (count($failedRows) > 0) {
                $filename = 'failed_import_accountsp_' . now()->format('Ymd_His') . '.csv';
                
                return $this->eximService->exportCSV(['filename' => $filename], $failedRows);
            }


            return redirect()->route('accountsp.index')->with('success', 'CSV uploaded and processed Successfully!');
        } catch (\Throwable $th) {
            return back()->with('error', 'Failed to import csv. Please try again.' . $th->getMessage());
        }
    }



    // Summaries
    public $summary_types = [
        'balance_sheet' => 'Neraca',
        // 'cashflow' => 'Cashflow',
        'profit_loss' => 'Laba Rugi',
    ];

    public function summary(Request $request)
    {
        $space_id = session('space_id') ?? null;
        if(is_null($space_id)){
            abort(403);
        }

        $space = Space::findOrFail($space_id);
        // $spaces = $space->spaceAndChildren();
        $spaces = collect([$space]);


        // generate data by date
        $validated = $request->validate([
            'summary_type' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $start_date = $validated['start_date'] ?? null;
        $end_date = $validated['end_date'] ?? now()->format('Y-m-d');
        $validated['summary_type'] = $validated['summary_type'] ?? '';

        $end_time = Carbon::parse($end_date)->endOfDay();
        
        $txs = Transaction::with('input', 'type', 'details', 'details.detail') 
                            ->where('model_type', 'JE')
                            ->where('space_type', 'SPACE')
                            ->whereIn('space_id', $spaces->pluck('id')->toArray())
                            ->where('sent_time', '<=', $end_time)
                            ->orderBy('sent_time', 'asc');

        if(!is_null($start_date) && $validated['summary_type'] != 'balance_sheet'){
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
        if($validated['summary_type'] == null){
            // return $data;
        }

        $account = [];
        foreach($txs as $tx){
            foreach($tx->details as $detail){
                $acc = $detail->detail;

                if(!isset($account[$acc->id])){
                    $account[$acc->id] = array_merge(
                        [
                            'id' => $acc->id,
                            'code' => $acc->code,
                            'name' => $acc->name,
                            'type_id' => $acc->type_id,
                        ],
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

                // $detail->tx = [
                //     "id" => $tx->id,
                //     "sent_time" => $tx->sent_time,
                //     "number" => $tx->number,
                //     "sender_notes" => $tx->sender_notes,
                // ];
                // $account[$acc->id]['details'][] = [
                //     'tx' => $detail->tx,
                //     'debit' => $detail->debit,
                //     'credit' => $detail->credit,
                //     'notes' => $detail->notes,
                // ];
            }
        }

        foreach($account as $key => $acc){
            $account[$key]['balance'] = ($account[$key]['debit'] - $account[$key]['credit']) * $acc['type']->debit;
        }
        $data->account = collect($account);

        switch($validated['summary_type']){
            case 'balance_sheet':
                $data->balance_sheet = $this->calculate_balance_sheet($data->account);
                break;
            case 'cashflow':
                $data->cashflow = $this->calculate_cashflow($data->account);
                break;
            case 'profit_loss':
                $data->profit_loss = $this->calculate_profit_loss($data->account);
                break;
            default: ;
        }

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


    public function calculate_balance_sheet($accounts){
        $data = [];
    
        $data['assets'] = $accounts->whereBetween('type_id', [1, 7]);
        $data['liabilities'] = $accounts->whereBetween('type_id', [8, 10]);
        $data['equities'] = $accounts->where('type_id', 11);
        $data['passive'] = $accounts->whereBetween('type_id', [8, 11]);

        $data['pnl'] = $this->calculate_profit_loss($accounts);
        // $data['pnl_before'] = $data['pnl'];

        $data['totalAssets'] = $data['assets']->sum('balance');
        $data['totalLiabilities'] = $data['liabilities']->sum('balance');
        $data['totalEquities'] = $data['equities']->sum('balance') 
                                    // + $data['pnl_before']['laba_bersih']
                                    + $data['pnl']['laba_bersih'];
    
        return $data;
    }



    // testing react
    public function getAccountTransactions(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|integer',
            'start_date' => 'nullable|date',
            'end_date' => 'required|date',
            'search' => 'nullable|string',
            'page' => 'nullable|integer',
            'per_page' => 'nullable|integer',
        ]);


        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 10);
        $offset = ($page - 1) * $perPage;
        $search = $request->get('search');


        $baseQuery = TransactionDetail::with(['transaction', 'detail'])
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->where('detail_id', $validated['account_id'])
            // ->whereBetween('sent_time', [
            //     Carbon::parse($validated['start_date'])->startOfDay(),
            //     Carbon::parse($validated['end_date'])->endOfDay()
            // ])
            ->select('transaction_details.*')
            ->orderBy('transactions.sent_time', 'asc');
        


        if ($search) {
            $baseQuery->whereHas('transaction', function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                ->orWhere('sender_notes', 'like', "%{$search}%");
            });
        }



        $initQuery = clone $baseQuery;
        $initBalance = 0;

        if(!is_null($validated['start_date']) && $validated['start_date'] != ''){
            $baseQuery->where('transactions.sent_time', '>=', Carbon::parse($validated['start_date'])->startOfDay());

            $initQuery->where('transactions.sent_time', '<', Carbon::parse($validated['start_date'])->startOfDay());
            $initTR = $initQuery->get();
            $initBalance += $initTR->sum(function ($item) {
                return floatval($item->debit ?? 0) - floatval($item->credit ?? 0);
            });
        }

        if(!is_null($validated['end_date']) && $validated['end_date'] != ''){
            $baseQuery->where('transactions.sent_time', '<=', Carbon::parse($validated['end_date'])->endOfDay());
        }



        // Total
        $total = (clone $baseQuery)->count();

        // Get full data before current page for initial balance
        $initials = (clone $baseQuery)
            ->skip(0)
            ->take($offset)
            ->get();

        $initialDebit = $initials->sum(function ($item) {
            return floatval($item->debit ?? 0);
        });
        $initialCredit = $initials->sum(function ($item) {
            return floatval($item->credit ?? 0);
        });
        $initialBalance = $initialDebit - $initialCredit + $initBalance;

        // Current page data
        $results = (clone $baseQuery)
            ->skip($offset)
            ->take($perPage)
            ->get();

        $pageDebit = $results->sum(function ($item) {
            return floatval($item->debit ?? 0);
        });
        $pageCredit = $results->sum(function ($item) {
            return floatval($item->credit ?? 0);
        });

        $finalBalance = $initialBalance + $pageDebit - $pageCredit;

        return response()->json([
            'total' => $total,
            'initial_balance' => $initialBalance,
            'final_balance' => $finalBalance,
            'initial_debit' => $initialDebit,
            'initial_credit' => $initialCredit,
            'page_debit' => $pageDebit,
            'page_credit' => $pageCredit,
            'page' => $page,
            'per_page' => $perPage,
            'data' => $results,
        ]);
    }
}
