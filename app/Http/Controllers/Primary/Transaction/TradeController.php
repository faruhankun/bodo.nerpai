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
use App\Models\Primary\Space;
use App\Models\Primary\Player;
use App\Models\Primary\Access\Variable;


class TradeController extends Controller
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
        return view('primary.transaction.trades.index');
    }


    // Buy
    public function indexPO()
    {
        return view('primary.transaction.trades.po');
    }

    public function indexSO()
    {
        return view('primary.transaction.trades.so');
    }


    public function create(Request $request)
    {
        $param = request()->all();
        $players = Player::all();
        
        $spaces = [];
        $space_id = session('space_id') ?? null;
        if($space_id){
            $space = Space::with('variables')->findOrFail($space_id);
            $spaces = $space->AllChildren();
            $spaces->prepend($space);
        } else {
            abort(403);
        }

        $spaces_with_variable_inventory = Variable::with('space')
                                                    ->where('key', 'space.setting.inventory')
                                                    ->whereNotNull('value')
                                                    ->get()
                                                    ->pluck('space');

        $spaces = $spaces->whereIn('id', $spaces_with_variable_inventory->pluck('id'));

        return view('primary.transaction.trades.create', compact('param', 'players', 'spaces'));
    }



    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'space_id' => 'required',

                'sender_id' => 'required',
                'sent_date' => 'nullable|date',
                'sender_notes' => 'nullable|string|max:255',
                'input_id' => 'nullable',
                
                'receiver_id' => 'required',
                'received_date' => 'nullable|date',
                'receiver_notes' => 'nullable|string|max:255',
                'output_id' => 'nullable',
            ]);

            // dd($validated);

            $tx = Transaction::create([
                'model_type' => 'TRD',
                'space_type' => 'SPACE',
                'space_id' => $validated['space_id'],

                'sender_type' => 'PLAY',
                'sender_id' => $validated['sender_id'],
                'sent_date' => $validated['sent_date'],
                'sender_notes' => $validated['sender_notes'],
                'input_type' => 'SPACE',
                'input_id' => $validated['input_id'],

                'receiver_type' => 'PLAY',
                'receiver_id' => $validated['receiver_id'],
                'received_date' => $validated['received_date'],
                'receiver_notes' => $validated['receiver_notes'],
                'output_type' => 'SPACE',
                'output_id' => $validated['output_id'],
            ]);

            $tx->generateNumber();
            $tx->save();

            return redirect()->route('trades.edit', $tx->id)
                            ->with('success', 'Transaction is Created Successfully!');
        } catch (\Throwable $th) {
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }



    public function edit(String $id)
    {
        $tx = Transaction::with(['details'])->findOrFail($id);

        $param = request()->all();
        $players = Player::all();
        
        $spaces = [];
        $space_id = session('space_id') ?? null;
        if($space_id){
            $space = Space::with('variables')->findOrFail($space_id);
            $spaces = $space->AllChildren();
            $spaces->prepend($space);
        } else {
            abort(403);
        }

        $spaces_with_variable_inventory = Variable::with('space')
                                                    ->where('key', 'space.setting.inventory')
                                                    ->whereNotNull('value')
                                                    ->get()
                                                    ->pluck('space');

        $spaces = $spaces->whereIn('id', $spaces_with_variable_inventory->pluck('id'));

        return view('primary.transaction.trades.edit', compact('tx', 'spaces', 'players'));
    }



    public function update(Request $request, $id)
    {
        try {
            // dd($request->all());
            
            $validated = $request->validate([
                'model_type' => 'required',
                'space_id' => 'required',
                'sender_id' => 'required',
                'sent_date' => 'nullable|date',
                'sender_notes' => 'nullable|string|max:255',
                'input_id' => 'nullable',
                'receiver_id' => 'nullable',
                'received_date' => 'nullable|date',
                'receiver_notes' => 'nullable|string|max:255',
                'output_id' => 'nullable',
            ]);

            // dd($validated);

            $tx = Transaction::with(['details'])->findOrFail($id);

            $tx->update($validated);

            if($tx->number == null){
                $tx->generateNumber();
                $tx->save();
            }

            return redirect()->route('trades.show', $tx->id)
                            ->with('success', "Transaction {$tx->number} Created Successfully!");
        } catch (\Throwable $th) {
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }


    public function show($id)
    {
        $tx = Transaction::with(['details'])->findOrFail($id);

        return view('primary.transaction.trades.show', compact('tx'));
    }


    public function destroy(String $id)
    {
        try {
            $journal_entry = Transaction::findOrFail($id);
            $journal_entry->delete();

            $journal_entry->details()->delete();

            return redirect()->route('trades.index')
                ->with('success', 'Journal Entry deleted successfully');
        } catch (\Throwable $th) {
            return back()->with('error', 'Failed to delete journal entry. Please try again.');
        }
    }



    public function getTradesPOData(){
        return $this->getTradesData('PO');
    }

    public function getTradesSOData(){
        return $this->getTradesData('SO');
    }


    public function getTradesData($model_type){
        $trades = [];

        $space_id = session('space_id') ?? null;

        if($model_type){
            $trades = Transaction::with('input', 'type')
                                ->orderBy('sent_time', 'desc');
        }

        
        if($space_id){
            // $trades = $trades->where('input_type', 'SPACE')
            //                 ->where('output_type', 'SPACE');
            if($model_type == 'SO'){
                $trades = $trades->where('input_id', $space_id);
            } else if($model_type == 'PO'){
                $trades = $trades->where('output_id', $space_id);
            }
        } 

        return DataTables::of($trades)
            ->addColumn('actions', function ($data) {
                $route = 'trades';
                
                $actions = [
                    'show' => 'button',
                    'edit' => 'button',
                    // 'delete' => 'button',
                ];

                return view('components.crud.partials.actions', compact('data', 'route', 'actions'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }
}
