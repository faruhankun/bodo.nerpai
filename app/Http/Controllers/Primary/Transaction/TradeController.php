<?php

namespace App\Http\Controllers\Primary\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Company\Finance\Account;
use App\Models\Company\Finance\JournalEntry;
use App\Models\Employee;

use App\Models\Primary\Transaction;
use App\Models\Primary\Inventory;
use App\Models\Primary\Space;
use App\Models\Primary\Player;
use App\Models\Primary\Access\Variable;


use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Services\Primary\Transaction\TradeService;
use App\Services\Primary\Transaction\JournalAccountService;


class TradeController extends Controller
{
    protected $journalEntryAccount;
    protected $tradeService;

    public function __construct(JournalAccountService $journalEntryAccount
                                , TradeService $tradeService)
    {
        $this->journalEntryAccount = $journalEntryAccount;
        $this->tradeService = $tradeService;
    }



    public function getData(Request $request){
        return $this->tradeService->getData($request);
    }



    // Export Import
    public function eximData(Request $request){
        $query = $request->get('query');
        
        try {
            switch($query){
                case 'importTemplate':
                    $response = $this->tradeService->getImportTemplate();
                    break;
                case 'export':
                    $response = $this->tradeService->exportData($request);
                    break;
                case 'import':
                    $response = $this->tradeService->importData($request);
                    break;
                default:
                    $response = response()->json(['message' => 'Invalid query', 'success' => false], 400);
                    break;

            }
            
            return $response;
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
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


    public function index(){ return view('primary.transaction.trades.index'); }



    public function store(Request $request)
    {
        $request_source = get_request_source($request);
        $space_id = get_space_id($request);

        try {
            $validated = $request->validate([
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
                'space_id' => $space_id,

                'sender_type' => 'PLAY',
                'sender_id' => $validated['sender_id'],
                'sent_time' => $validated['sent_date'] ?? now()->format('Y-m-d'),
                'sender_notes' => $validated['sender_notes'] ?? null,
                'input_type' => 'SPACE',
                'input_id' => $validated['input_id'] ?? null,

                'receiver_type' => 'PLAY',
                'receiver_id' => $validated['receiver_id'],
                'received_time' => $validated['received_date'] ?? null,
                'receiver_notes' => $validated['receiver_notes'] ?? null,
                'output_type' => 'SPACE',
                'output_id' => $validated['output_id'] ?? null,
            ]);

            $tx->generateNumber();
            $tx->save();


            if($request_source == 'api'){
                return response()->json(['message' => 'Transaction is Created Successfully!', 'success' => true, 'data' => $tx], 200);
            }

            return redirect()->route('trades.edit', $tx->id)
                            ->with('success', 'Transaction is Created Successfully!');
        } catch (\Throwable $th) {
            if($request_source == 'api')
                return response()->json(['message' => $th->getMessage(), 'success' => false], 500);

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
        $request_source = get_request_source($request);

        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'sender_id' => 'required',
                'sent_date' => 'nullable|date',
                'sender_notes' => 'nullable|string|max:255',
                'input_id' => 'nullable',

                'receiver_id' => 'nullable',
                'received_date' => 'nullable|date',
                'receiver_notes' => 'nullable|string|max:255',
                'output_id' => 'nullable',

                'details' => 'nullable|array',
                'details.*.detail_id' => 'required',
                'details.*.quantity' => 'nullable|numeric|min:0',
                'details.*.price' => 'nullable|numeric|min:0',
                'details.*.discount' => 'nullable|numeric|min:0',
                'details.*.cost_per_unit' => 'nullable|numeric|min:0',
                'details.*.notes' => 'nullable|string|max:255',
            ]);
            if(!isset($validated['details'])){
                $validated['details'] = [];
            }


            $tx = Transaction::with(['details'])->findOrFail($id);

            $this->tradeService->updateData($tx, $validated, $validated['details']);



            DB::commit();
            if($request_source == 'api'){
                return response()->json(['message' => 'Transaction is Updated Successfully!', 'success' => true, 'data' => array($tx)], 200);
            }

            return redirect()->route('trades.show', $tx->id)
                            ->with('success', "Transaction {$tx->number} Created Successfully!");
        } catch (\Throwable $th) {
            DB::rollBack();

            if($request_source == 'api')
                return response()->json(['message' => $th->getMessage(), 'success' => false], 500);

            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }



    public function show(Request $request, String $id)
    {
        $request_source = get_request_source($request);

        try {
            $tx = Transaction::with(['details', 'details.detail', 'details.detail.item',
                                    'sender', 'receiver'])->findOrFail($id);
        } catch (\Throwable $th) {
            if($request_source == 'api'){
                return response()->json([
                    'data' => [],
                    'success' => false,
                    'message' => $th->getMessage(),
                ], 400);
            }
        }


        if($request_source == 'api'){
            return response()->json([
                'data' => array($tx),
                'recordFiltered' => 1,
                'success' => true,
            ]);
        }

        return view('primary.transaction.trades.show', compact('tx'));
    }



    public function destroy(Request $request, String $id)
    {
        $request_source = get_request_source($request);

        DB::beginTransaction();

        try {
            $tx = Transaction::findOrFail($id);
            $tx->delete();

            $tx->details()->delete();


            DB::commit();
            if($request_source == 'api'){
                return response()->json([
                    'data' => $tx,
                    'success' => true,
                    'message' => 'Trades deleted successfully',
                ]);
            }
            return redirect()->route('trades.index')
                ->with('success', 'Trades deleted successfully');
        } catch (\Throwable $th) {
            DB::rollBack();

            if($request_source == 'api'){
                return response()->json(['message' => $th->getMessage(), 'success' => false], 404);
            }
            return back()->with('error', 'Failed to delete trades. Please try again.');
        }
    }
}
