<?php

namespace App\Http\Controllers\Primary\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Company\Finance\Account;
use App\Models\Company\Finance\JournalEntry;
use App\Models\Employee;
use App\Services\Primary\Transaction\JournalAccountService;
use Illuminate\Http\Request;

use Yajra\DataTables\Facades\DataTables;

use App\Models\Primary\Transaction;
use App\Models\Primary\Inventory;

class JournalAccountController extends Controller
{
    protected $journalEntryAccount;

    public function __construct(JournalAccountService $journalEntryAccount)
    {
        $this->journalEntryAccount = $journalEntryAccount;
    }


    public function get_account()
    {
        $space_id = session('space_id') ?? null;

        $accountsp = Inventory::with('type', 'parent')->where('model_type', 'ACC')->get();

        if($space_id){
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
                'date' => 'required|date',
                'description' => 'nullable|string|max:255',
                'journal_entry_details' => 'required|array',
                'journal_entry_details.*.account_id' => 'required',
                'journal_entry_details.*.debit' => 'required|numeric|min:0',
                'journal_entry_details.*.credit' => 'required|numeric|min:0',
                'journal_entry_details.*.notes' => 'nullable|string|max:255',
            ]);

            $totalDebit = array_sum(array_column($validated['journal_entry_details'], 'debit'));
            $totalCredit = array_sum(array_column($validated['journal_entry_details'], 'credit'));

            if ($totalDebit != $totalCredit) {
                return back()->with('error', 'Total debits and credits must be equal.');
            }

            $player = auth()->user()->player;
            $data = [
                'space_id' => $validated['space_id'],
                'sender_id' => $player->id,
                'send_time' => $validated['date'],
                'sender_notes' => $validated['description'],
                'total' => $totalDebit,
            ];

            $journal_entry = $this->journalEntryAccount->addJournalEntry($data, $validated['journal_entry_details']);

            return redirect()->route('journal_accounts.index')->with('success', 'Journal Entry Created Successfully!');
        } catch (\Throwable $th) {
            return back()->with('error', 'Something went wrong. Please try again.');
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



    public function getJournalAccountsData(){
        $journal_accounts = [];

        $space_id = session('space_id') ?? null;

        $journal_accounts = Transaction::with('input', 'type')->where('model_type', 'JE')->get();

        if($space_id){
            $journal_accounts = $journal_accounts->where('space_type', 'SPACE')
                                    ->whereIn('space_id', $space_id);
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
}
