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



    public function create()
    {
        $accountsp = $this->get_account();
        $employee = session('employee');
        return view('primary.transaction.journal_accounts.create', compact('accountsp', 'employee'));
    }



    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'space_id' => 'required',
                'sender_id' => 'required',
                'sent_time' => 'required',
                'sender_notes' => 'nullable|string|max:255',
            ]);

            $data = [
                'space_id' => $validated['space_id'],
                'sender_id' => $validated['sender_id'],
                'sent_time' => $validated['sent_time'],
                'sender_notes' => $validated['sender_notes'],
            ];

            $journal_entry = $this->journalEntryAccount->addJournalEntry($data);

            return redirect()->route('journal_accounts.edit', $journal_entry->id)
                            ->with('success', 'Journal Entry Created Successfully!');
        } catch (\Throwable $th) {
            return back()->with('error', 'Something went wrong. Please try again.' . $th->getMessage());
        }
    }



    public function edit(String $id)
    {
        $accountsp = $this->get_account();
        $journal_entry = Transaction::with(['details'])->findOrFail($id);

        return view('primary.transaction.journal_accounts.edit', compact('journal_entry', 'accountsp'));
    }



    public function update(String $id, Request $request)
    {
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

            $player = auth()->user()->player;
            $data = [
                'sent_time' => $validated['sent_time'],
                'sender_notes' => $validated['sender_notes'],
                'handler_type' => 'PLAY',
                'handler_id' => $player->id,
                'total' => $totalDebit,
            ];

            $this->journalEntryAccount->updateJournalEntry($journal_entry, $data, $validated['details']);

            return redirect()->route('journal_accounts.index')
                ->with('success', 'Journal Entry updated successfully!');
        } catch (\Throwable $th) {
            // Log the error if needed
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }



    public function destroy(String $id)
    {
        try {
            $journal_entry = Transaction::findOrFail($id);
            $journal_entry->delete();

            $journal_entry->details()->delete();

            return redirect()->route('journal_accounts.index')
                ->with('success', 'Journal Entry deleted successfully');
        } catch (\Throwable $th) {
            return back()->with('error', 'Failed to delete journal entry. Please try again.');
        }
    }



    public function getJournalAccountsData()
    {
        $space_id = session('space_id') ?? null;

        $journal_accounts = Transaction::with('input', 'type')->where('model_type', 'JE')
            ->orderBy('sent_time', 'desc');

        if ($space_id) {
            $journal_accounts = $journal_accounts->where('space_type', 'SPACE')
                                                ->where('space_id', $space_id);
        } else {
            $journal_accounts->whereRaw('1 = 0');
        }

        return DataTables::of($journal_accounts)
            ->addColumn('actions', function ($data) {
                $route = 'journal_accounts';

                $actions = [
                    'show' => 'modal',
                    'show_modal' => 'primary.transaction.journal_accounts.show',
                    'edit' => 'button',
                    'delete' => 'button',
                ];

                return view('components.crud.partials.actions', compact('data', 'route', 'actions'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    
    
    public function readCsv(Request $request)
    {
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
                    'sent_time'    => \Carbon\Carbon::createFromFormat('d/m/Y', $first['date'])->toDateString(),
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

            return redirect()->route('journal_accounts.index')->with('success', 'CSV uploaded and processed Successfully!');
        } catch (\Throwable $th) {
            dd($th);
            return back()->with('error', 'Failed to import csv. Please try again.');
        }
    }


    // Export Import
    public function importTemplate(){
        $response = $this->eximService->exportCSV(['filename' => 'journal_import_template.csv'], $this->import_columns);

        return $response;
    }


    public function importData(Request $request) {
        return $this->readCsv($request);
    }


    public function exportData(){
        return back()->with('error', 'Under Construction');
    }
}
