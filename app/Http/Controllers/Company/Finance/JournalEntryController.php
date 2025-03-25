<?php

namespace App\Http\Controllers\Company\Finance;

use App\Http\Controllers\Controller;
use App\Models\Company\Finance\Account;
use App\Models\Company\Finance\JournalEntry;
use App\Models\Employee;
use App\Services\Company\Finance\JournalEntryService;
use Illuminate\Http\Request;

class JournalEntryController extends Controller
{
    protected $journalEntryService;

    public function __construct(JournalEntryService $journalEntryService)
    {
        $this->journalEntryService = $journalEntryService;
    }
    public function index()
    {
        $journal_entries = JournalEntry::all();
        return view('company.finance.journal_entries.index', compact('journal_entries'));
    }

    public function create()
    {
        $accounts = Account::all();
        $employee = session('employee');
        return view('company.finance.journal_entries.create', compact('accounts', 'employee'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'date' => 'required|date',
                'description' => 'required|string|max:255',
                'journal_entry_details' => 'required|array',
                'journal_entry_details.*.account_id' => 'required|exists:accounts,id',
                'journal_entry_details.*.debit' => 'required|numeric|min:0',
                'journal_entry_details.*.credit' => 'required|numeric|min:0',
                'journal_entry_details.*.notes' => 'nullable|string|max:255',
            ]);

            $totalDebit = array_sum(array_column($validated['journal_entry_details'], 'debit'));
            $totalCredit = array_sum(array_column($validated['journal_entry_details'], 'credit'));

            if ($totalDebit != $totalCredit) {
                return back()->with('error', 'Total debits and credits must be equal.');
            }

            $data = [
                'date' => $validated['date'],
                'description' => $validated['description'],
                'total' => $totalDebit,
            ];

            $journal_entry = $this->journalEntryService->addJournalEntry($data, $validated['journal_entry_details']);

            return redirect()->route('journal_entries.index')->with('success', 'Journal Entry Created Successfully!');
        } catch (\Throwable $th) {
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function show(String $id)
    {
        $journal_entry = JournalEntry::with(['journal_entry_details', 'created_by'])->findOrFail($id);

        return view('company.finance.journal_entries.show', compact('journal_entry'));
    }

    public function edit(String $id)
    {
        $accounts = Account::all();
        $journal_entry = JournalEntry::with(['journal_entry_details'])->findOrFail($id);
        return view('company.finance.journal_entries.edit', compact('journal_entry', 'accounts'));
    }

    public function update(String $id, Request $request)
    {
        try {
            $validated = $request->validate([
                'date' => 'required|date',
                'description' => 'required|string|max:255',
                'journal_entry_details' => 'required|array',
                'journal_entry_details.*.account_id' => 'required|exists:accounts,id',
                'journal_entry_details.*.debit' => 'required|numeric|min:0',
                'journal_entry_details.*.credit' => 'required|numeric|min:0',
                'journal_entry_details.*.notes' => 'nullable|string|max:255',
            ]);

            $journal_entry = JournalEntry::with(['journal_entry_details'])->findOrFail($id);

            $totalDebit = array_sum(array_column($validated['journal_entry_details'], 'debit'));
            $totalCredit = array_sum(array_column($validated['journal_entry_details'], 'credit'));

            if ($totalDebit != $totalCredit) {
                return back()->with('error', 'Total debits and credits must be equal.');
            }

            $data = [
                'date' => $validated['date'],
                'description' => $validated['description'],
                'total' => $totalDebit,
            ];

            $this->journalEntryService->updateJournalEntry($journal_entry, $data, $validated['journal_entry_details']);

            return redirect()->route('journal_entries.index')
                ->with('success', 'Journal Entry updated successfully!');
        } catch (\Throwable $th) {
            // Log the error if needed
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function destroy(String $id)
    {
        try {
            JournalEntry::findOrFail($id)->delete();

            return redirect()->route('journal_entries.index')
                ->with('success', 'Journal Entry deleted successfully');
        } catch (\Throwable $th) {
            return back()->with('error', 'Failed to delete journal entry. Please try again.');
        }
    }
}
