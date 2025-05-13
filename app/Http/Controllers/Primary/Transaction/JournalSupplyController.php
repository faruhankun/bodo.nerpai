<?php

namespace App\Http\Controllers\Primary\Transaction;

use App\Http\Controllers\Controller;
use App\Services\Primary\Transaction\JournalSupplyService;
use Illuminate\Http\Request;

use Yajra\DataTables\Facades\DataTables;

use App\Models\Primary\Transaction;
use App\Models\Primary\Inventory;
use App\Models\Primary\TransactionDetail;
use App\Models\Primary\Item;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class JournalSupplyController extends Controller
{
    protected $journalSupply;

    protected $model_types = [
        ['id' => 'PO', 'name' => 'Purchase'],
        ['id' => 'SO', 'name' => 'Sales'],
        ['id' => 'FND', 'name' => 'Opname Found'],
        ['id' => 'LOSS', 'name' => 'Opname Loss'],
        ['id' => 'DMG', 'name' => 'Damage'],
        ['id' => 'RTR', 'name' => 'Return'],
        ['id' => 'MV', 'name' => 'Move'],
    ];

    public function __construct(JournalSupplyService $journalSupply)
    {
        $this->journalSupply = $journalSupply;
    }


    public function get_inventories()
    {
        $space_id = session('space_id') ?? null;

        $inventories = Inventory::with('type', 'parent')->where('model_type', 'SUP');

        if ($space_id) {
            $inventories = $inventories->where('space_type', 'SPACE')
                                    ->where('space_id', $space_id);
        }

        $inventories = $inventories->get();
        return $inventories;
    }


    public function index()
    {
        return view('primary.transaction.journal_supplies.index');
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

            $journal = $this->journalSupply->addJournal($data);

            return redirect()->route('journal_supplies.index')->with('success', "Journal {$journal->id} Created Successfully!");
        } catch (\Throwable $th) {
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }



    public function edit(String $id)
    {
        $inventories = $this->get_inventories();
        $inventories = Item::with('type', 'parent')
                            ->where('model_type', 'PRD')->get();
        $journal = Transaction::with(['details'])->findOrFail($id);
        $model_types = $this->model_types;

        return view('primary.transaction.journal_supplies.edit', compact('journal', 'inventories', 'model_types'));
    }



    public function update(String $id, Request $request)
    {
        try {
            $validated = $request->validate([
                'sent_time' => 'required',
                'handler_id' => 'required',
                'handler_notes' => 'nullable|string|max:255',
                'details' => 'nullable|array',
                'details.*.item_id' => 'required',
                'details.*.quantity' => 'required|numeric',
                'details.*.model_type' => 'required|string',
                'details.*.cost_per_unit' => 'required|min:0',
                'details.*.notes' => 'nullable|string|max:255',
            ]);

            if(!isset($validated['details'])){
                $validated['details'] = [];
            }

            //dd($validated);

            $journal = Transaction::with(['details'])->findOrFail($id);

            $data = [
                'sent_time' => $validated['sent_time'],
                'handler_notes' => $validated['handler_notes'],
                'handler_type' => 'PLAY',
                'handler_id' => $validated['handler_id'],
            ];

            $this->journalSupply->updateJournal($journal, $data, $validated['details']);

            return redirect()->route('journal_supplies.index')
                ->with('success', "Journal {$journal->id} updated successfully!");
        } catch (\Throwable $th) {
            // Log the error if needed
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }



    public function destroy(String $id)
    {
        try {
            $journal = Transaction::findOrFail($id);
            $journal->delete();

            $journal->details()->delete();

            return redirect()->route('journal_supplies.index')
                ->with('success', 'Journal Entry deleted successfully');
        } catch (\Throwable $th) {
            return back()->with('error', 'Failed to delete journal entry. Please try again.');
        }
    }



    public function getJournalSuppliesData()
    {
        $space_id = session('space_id') ?? null;
        if(is_null($space_id)){
            abort(403);
        }

        $journal_supplies = Transaction::with('input', 'type')
            ->where('model_type', 'JS')
            ->orderBy('sent_time', 'desc');

        $journal_supplies = $journal_supplies->where('space_type', 'SPACE')
                                            ->where('space_id', $space_id);
                          
                                            
        return DataTables::of($journal_supplies)
            ->addColumn('actions', function ($data) {
                $route = 'journal_supplies';

                $actions = [
                    'show' => 'modal',
                    'show_modal' => 'primary.transaction.journal_supplies.show',
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
                    // look up or create inventories, then use its id
                    $acct = Inventory::where('code', $row['account_code'])
                        ->where('space_id', session('space_id'))
                        ->first();
                    if (!$acct) {
                        $acct = Inventory::create([
                            'name' => $row['account_name'],
                            'type_id' => SupplyType::where('basecode', '1-101')->first()->id,
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
                $this->journalSupply->addJournalEntry($entryData, $details);
            }

            return redirect()->route('journal_supplies.index')->with('success', 'CSV uploaded and processed Successfully!');
        } catch (\Throwable $th) {
            dd($th);
            return back()->with('error', 'Failed to import csv. Please try again.');
        }
    }

    public function downloadTemplate()
    {
        $headers = ['Content-Type' => 'text/csv'];
        $filename = "template.csv";

        // Define your column headers (template)
        $columns = ['date', 'number', 'description', 'account_code', 'account_name', 'notes', 'debit', 'credit', 'tags'];

        // Open a memory "file" for writing CSV data
        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fclose($file);
        };

        return Response::stream($callback, 200, [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ]);
    }
}
