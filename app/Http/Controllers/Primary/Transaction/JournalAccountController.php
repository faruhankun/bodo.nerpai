<?php

namespace App\Http\Controllers\Primary\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Company\Finance\Account;
use App\Models\Company\Finance\AccountType;
use App\Services\Primary\Transaction\JournalAccountService;
use App\Services\Primary\Basic\EximService;
use Illuminate\Http\Request;

use Yajra\DataTables\Facades\DataTables;

use App\Models\Primary\Transaction;
use App\Models\Primary\Inventory;
use App\Models\Primary\TransactionDetail;
use Illuminate\Support\Facades\Response;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class JournalAccountController extends Controller
{
    protected $journalEntryAccount;
    protected $eximService;

    public $import_columns = ['date', 'number', 'description', 'account_code', 'account_name', 'notes', 'debit', 'credit', 'tags'];



    public function __construct(JournalAccountService $journalEntryAccount, EximService $eximService)
    {
        $this->journalEntryAccount = $journalEntryAccount;
        $this->eximService = $eximService;
    }


    public function get_account()
    {
        $space_id = session('space_id') ?? null;

        $accountsp = Inventory::with('type', 'parent')->where('model_type', 'ACC')->get();

        if ($space_id) {
            $accountsp = $accountsp->where('space_type', 'SPACE')
                ->whereIn('space_id', $space_id);
        }

        return $accountsp;
    }


    public function index()
    {
        return view('primary.transaction.journal_accounts.index');
    }



    public function show(Request $request, String $id)
    {
        $request_source = get_request_source($request);

        try {
            $data = Transaction::with(['details', 'details.detail'])->findOrFail($id);
        } catch (\Throwable $th) {
            if($request_source == 'api'){
                return response()->json([
                    'data' => [],
                    'success' => false,
                    'error' => $th->getMessage(),
                ]);
            }

            return back()->with('error', 'Journal Entry not found.');
        }

        if($request_source == 'api'){
            return response()->json([
                'data' => array($data),
                'recordFiltered' => 1,
                'success' => true,
            ]);
        }

        return view('primary.transaction.journal_accounts.before.show', compact('data'));
    }


    public function create()
    {
        $accountsp = $this->get_account();
        $employee = session('employee');
        return view('primary.transaction.journal_accounts.create', compact('accountsp', 'employee'));
    }



    public function store(Request $request)
    {
        $request_source = get_request_source($request);
        $space_id = get_space_id($request);

        try {
            $validated = $request->validate([
                'sender_id' => 'required',
                'sent_time' => 'required',
                'sender_notes' => 'nullable|string|max:255',
            ]);

            $data = [
                'space_id' => $space_id,
                'sender_id' => $validated['sender_id'],
                'sent_time' => $validated['sent_time'],
                'sender_notes' => $validated['sender_notes'],
            ];

            $journal_entry = $this->journalEntryAccount->addJournalEntry($data);
        } catch (\Throwable $th) {
            if($request_source == 'api'){
                return response()->json([
                    'data' => [],
                    'success' => false,
                    'message' => $th->getMessage(),
                ]);
            }

            return back()->with('error', 'Something went wrong. Please try again.' . $th->getMessage());
        }

        if($request_source == 'api'){
            return response()->json([
                'data' => array($journal_entry),
                'message' => 'Journal Entry Created Successfully!',
                'success' => true,
            ]);
        }

        return redirect()->route('journal_accounts.edit', $journal_entry->id)
                        ->with('success', 'Journal Entry Created Successfully!');
    }



    public function edit(String $id)
    {
        $accountsp = $this->get_account();
        $journal_entry = Transaction::with(['details', 'details.detail'])->findOrFail($id);

        return view('primary.transaction.journal_accounts.edit', compact('journal_entry', 'accountsp'));
    }



    public function update(String $id, Request $request)
    {
        $request_source = get_request_source($request);

        try {
            $validated = $request->validate([
                'sent_time' => 'required',
                'sender_notes' => 'nullable|string|max:255',
                'details' => 'required|array',
                'details.*.detail_id' => 'required',
                'details.*.debit' => 'required|numeric|min:0',
                'details.*.credit' => 'required|numeric|min:0',
                'details.*.notes' => 'nullable|string|max:255',
            ]);

            $journal_entry = Transaction::with(['details'])->findOrFail($id);

            $totalDebit = array_sum(array_column($validated['details'], 'debit'));
            $totalCredit = array_sum(array_column($validated['details'], 'credit'));

            if ($totalDebit != $totalCredit) {
                return back()->with('error', 'Total debits and credits must be equal.');
            }


            $player = Auth::user()->player;
            $data = [
                'sent_time' => $validated['sent_time'],
                'sender_notes' => $validated['sender_notes'],
                'handler_type' => 'PLAY',
                'handler_id' => $player->id,
                'total' => $totalDebit,
            ];

            $this->journalEntryAccount->updateJournalEntry($journal_entry, $data, $validated['details']);
        } catch (\Throwable $th) {
            if($request_source == 'api'){
                return response()->json([
                    'data' => [],
                    'success' => false,
                    'message' => $th->getMessage(),
                ]);
            }

            return back()->with('error', 'Something went wrong. Please try again.');
        }

        if($request_source == 'api'){
            return response()->json([
                'data' => array($journal_entry),
                'message' => 'Journal Entry Updated Successfully!',
                'success' => true,
            ]);
        }

        return redirect()->route('journal_accounts.show', $journal_entry->id)
                        ->with('success', 'Journal Entry Updated Successfully!');
    }



    public function destroy(Request $request, String $id)
    {
        $request_source = get_request_source($request);

        DB::beginTransaction();

        try {
            $journal_entry = Transaction::findOrFail($id);
            $journal_entry->delete();

            $journal_entry->details()->delete();
        } catch (\Throwable $th) {
            DB::rollBack();

            if($request_source == 'api'){
                return response()->json([
                    'data' => [],
                    'success' => false,
                    'message' => $th->getMessage(),
                ]);
            }

            return back()->with('error', 'Failed to delete journal entry. Please try again.');
        }

        
        DB::commit();

        if($request_source == 'api'){
            return response()->json([
                'data' => $journal_entry,
                'success' => true,
                'message' => 'Journal Entry deleted successfully',
            ]);
        }

        return redirect()->route('journal_accounts.index')
            ->with('success', 'Journal Entry deleted successfully');
    }


    public function getDataTable(Request $request)
    {
        return $this->journalEntryAccount->getJournalDT($request);
    }


    public function getJournalAccountsData(Request $request)
    {
        $space_id = session('space_id') ?? null;

        $journal_accounts = Transaction::with('input', 'type', 'details', 'details.detail')
                    ->where('model_type', 'JE')
                    ->orderBy('sent_time', 'desc');

        if ($space_id) {
            $journal_accounts = $journal_accounts->where('space_type', 'SPACE')
                                                ->where('space_id', $space_id);
        } else {
            $journal_accounts->whereRaw('1 = 0');
        }

        
        

        // search
        if ($request->has('search') && $request->search['value'] || $request->filled('q')) {
            
        }


        return DataTables::of($journal_accounts)
            ->addColumn('actions', function ($data) {
                $route = 'journal_accounts';

                $actions = [
                    'show' => 'modal',
                    'show_modal' => 'primary.transaction.journal_accounts.show-modal',
                    'edit' => 'button',
                    'delete' => 'button',
                ];

                return view('components.crud.partials.actions', compact('data', 'route', 'actions'))->render();
            })
            ->filter(function ($query) use ($request) {
                $search = $request->search['value'] ?? $request->q;

                $query = $query->where(function ($q) use ($search) {
                    $q->where('transactions.id', 'like', "%{$search}%")
                        ->orWhere('transactions.number', 'like', "%{$search}%")
                        ->orWhere('transactions.sent_time', 'like', "%{$search}%")
                        ->orWhere('transactions.handler_notes', 'like', "%{$search}%");

                    $q->orWhereHas('details', function ($q2) use ($search) {
                        $q2->where('transaction_details.notes', 'like', "%{$search}%")
                            ->orWhere('transaction_details.model_type', 'like', "%{$search}%")
                        ;
                    });

                    $q->orWhereHas('details.detail', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%")
                        ;
                    });
                });
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    
    
    public function readCsv(Request $request)
    {
        $request_source = get_request_source($request);

        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'file' => 'required|mimes:csv,txt'
            ]);

            $file = $validated['file'];
            $data = [];

            // Read the CSV into an array of associative rows
            if (($handle = fopen($file->getRealPath(), 'r')) !== FALSE) {
                $headers = fgetcsv($handle);
                while (($row = fgetcsv($handle)) !== FALSE) {
                    $record = [];
                    foreach ($headers as $i => $header) {
                        $record[trim($header, " *")] = $row[$i] ?? null;
                    }
                    $data[] = $record;
                }
                fclose($handle);
            }

            // Group by transaction number
            $grouped = [];
            foreach ($data as $row) {
                $grouped[$row['number']][] = $row;
            }

            foreach ($grouped as $txnNumber => $rows) {
                $first = $rows[0];

                // Prepare the journal entry header
                $entryData = [
                    'number'       => $txnNumber,
                    'space_id'     => session('space_id'),
                    'sender_id'    => auth()->user()->player->id,
                    'sent_time'    => empty($first['date']) ? Date('Y-m-d') : $first['date'],
                    'sender_notes' => $first['description'] ?? null,
                    'total'        => 0, // will be recalculated below
                ];

                $details = [];
                $total = 0;

                foreach ($rows as $row) {
                    // look up or create inventory, then use its id
                    $acct = Inventory::where('code', $row['account_code'])
                        ->where('space_id', session('space_id'))
                        ->first();
                    if (!$acct) {
                        $acct = Inventory::create([
                            'name' => $row['account_name'],
                            'type_id' => AccountType::where('basecode', '1-101')->first()->id,
                            'code' => $row['account_code'],
                            'space_type' => 'SPACE',
                            'space_id' => session('space_id'),
                            'model_type' => 'ACC',
                            'type_type' => 'ACCT',
                            'parent_type' => 'IVT',
                            'status' => 'active',
                        ]);
                    }

                    $debit  = floatval($row['debit'] ?? 0);
                    $credit = floatval($row['credit'] ?? 0);

                    $details[] = [
                        'account_id' => $acct->id,
                        'debit'      => $debit,
                        'credit'     => $credit,
                        'notes'      => $row['notes'] ?? null,
                    ];

                    // accumulate for the header total
                    $total += $debit;
                }

                $entryData['total'] = $total;

                // Delegate to the service
                $this->journalEntryAccount->addJournalEntry($entryData, $details);
            }
        } catch (\Throwable $th) {
            DB::rollBack();

            if($request_source == 'api'){
                return response()->json([
                    'data' => [],
                    'success' => false,
                    'message' => $th->getMessage(),
                ]);
            }

            return back()->with('error', 'Failed to import csv. Error:' . $th->getMessage());
        }

        DB::commit();

        if($request_source == 'api'){
            return response()->json([
                'data' => [],
                'success' => true,
                'message' => 'CSV uploaded and processed Successfully!',
            ]);
        }

    
        return redirect()->route('journal_accounts.index')->with('success', 'CSV uploaded and processed Successfully!');
    }


    
    // Export Import
    public function importTemplate(){
        $response = $this->eximService->exportCSV(['filename' => 'journal_import_template.csv'], $this->import_columns);

        return $response;
    }


    public function importData(Request $request) {
        return $this->readCsv($request);
    }


    public function exportData(Request $request) {
        $request_source = get_request_source($request);

        if($request_source == 'api'){
            return response()->json([
                'data' => [],
                'success' => false,
                'message' => 'Under Construction',
            ]);
        }

        return back()->with('error', 'Under Construction');
    }
}
