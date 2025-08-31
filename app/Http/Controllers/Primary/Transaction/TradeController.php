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
                'sent_time' => 'nullable',
                'sender_notes' => 'nullable|string|max:255',
            ]);

            $data = [
                'space_id' => $space_id,
                'sender_id' => $validated['sender_id'],
                'sent_time' => $validated['sent_time'] ?? now(),
                'sender_notes' => $validated['sender_notes'],
            ];

            $journal = $this->tradeService->addJournal($data, $request);


            if($request_source == 'api'){
                return response()->json([
                    'data' => array($journal),
                    'success' => true,
                    'message' => "Journal {$journal->id} Created Successfully!",
                ]);
            }


            return redirect()->route('trades.edit', $journal->id)
                            ->with('success', "Journal {$journal->id} Created Successfully!");
        } catch (\Throwable $th) {
            if($request_source == 'api'){
                return response()->json([
                    'data' => [],
                    'success' => false,
                    'message' => $th->getMessage(),
                ]);
            }

            return back()->with('error', 'Error: ' . $th->getMessage());
        }
    }



    public function edit(String $id)
    {
        $journal = Transaction::with(['details', 'details.detail',
                                        'parent', 
                                        'input', 'outputs', 'sender', 'receiver'])->findOrFail($id);
        $model_types = $this->tradeService->model_types;
        $status_types = $this->tradeService->status_types;

        return view('primary.transaction.trades.edit', compact('journal', 'model_types', 'status_types'));
    }



    public function update(String $id, Request $request)
    {
        $request_source = get_request_source($request);

        try {
            $validated = $request->validate([
                'sent_time' => 'nullable',
                
                'handler_id' => 'required',
                'handler_notes' => 'nullable|string|max:255',
                
                'receiver_id' => 'nullable',
                'receiver_notes' => 'nullable|string|max:255',

                'space_origin' => 'nullable',

                'details' => 'nullable|array',
                'details.*.detail_id' => 'required',
                'details.*.model_type' => 'required|string',
                'details.*.quantity' => 'required|numeric',
                'details.*.price' => 'required|min:0',
                'details.*.discount' => 'nullable|min:0',
                'details.*.notes' => 'nullable',

                'status' => 'nullable|string|max:255',

                'parent_id' => 'nullable',

                'old_files.*' => 'nullable|array',
                'files.*' => 'nullable|file|max:2048',
            ]);

            if(!isset($validated['details'])){
                $validated['details'] = [];
            }

            $journal = Transaction::with(['details'])->findOrFail($id);



            // handling files
            // Ambil file lama yang masih dipertahankan
            $oldFiles = $request->input('old_files', []); // array path lama

            $finalFiles = [];
            foreach ($oldFiles as $old_file) {
                $finalFiles[] = [
                    'name' => $old_file['name'],
                    'path' => $old_file['path'],
                    'size' => $old_file['size'],
                ];
            }

            // Upload file baru
            if ($request->hasFile('files')) {

                foreach ($request->file('files') as $file) {
                    $path = $file->store('uploads/transactions/' . $journal->id , 'public');
                    $finalFiles[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => 'storage/'.$path,
                        'size' => $file->getSize(),
                    ];
                }
            }



            $data = [
                'sent_time' => $validated['sent_time'] ?? now(),
                'receiver_type' => 'PLAY',
                'receiver_id' => $validated['receiver_id'] ?? null,
                'receiver_notes' => $validated['receiver_notes'] ?? null,

                'handler_notes' => $validated['handler_notes'] ?? null,
                'handler_type' => 'PLAY',
                'handler_id' => $validated['handler_id'],

                'status' => $validated['status'] ?? null,

                'files' => $finalFiles,
            ];

            if(isset($validated['parent_id'])){ 
                $data['parent_type'] = 'TX';
                $data['parent_id'] = $validated['parent_id']; 
            }

            $journal = $this->tradeService->updateJournal($journal, $data, $validated['details']);



            if($request_source == 'api'){
                return response()->json([
                    'data' => array($journal),
                    'success' => true,
                    'message' => "Journal {$journal->id} updated successfully!",
                ]);
            }

            return redirect()->route('trades.show', $journal->id)
                ->with('success', "Journal {$journal->id} updated successfully!");
        } catch (\Throwable $th) {
            if($request_source == 'api'){
                return response()->json(['message' => $th->getMessage(), 'success' => false], 404);
            }

            return back()->with('error', 'Something went wrong. error:' . $th->getMessage());
        }
    }



    public function invoice(Request $request, String $id)
    {
        $data = Transaction::with(['details', 'details.detail', 'input', 'outputs',
                                'sender', 'receiver'])->findOrFail($id);

        $invoice_type = $request->input('invoice_type') ?? 'invoice';

        return view('primary.transaction.trades.partials.' . $invoice_type, compact('data'));
    }



    public function show(Request $request, String $id)
    {
        $request_source = get_request_source($request);

        $tx = null;

        try {
            $tx = Transaction::with(['details', 'details.detail', 
                                    'parent', 'children',
                                    'input', 'outputs',
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



        // tx related
        $data = $tx;
        $tx_related = $data->outputs ?? collect();
        if($data->input){
            $tx_related->push($data->input);
        }

        if($data->parent){
            $tx_related->push($data->parent);
        }

        if($data->children){
            $tx_related = $tx_related->merge($data->children);
        }



        // for page
        $get_page_show = $request->get('page_show') ?? null;
        $page_show = 'null';
        if($get_page_show){
            $get_page_show = 'show';
            $page_show = view('primary.transaction.trades.partials.datashow', compact('data', 'tx_related', 'get_page_show'))->render();
        }

        if($request_source == 'api'){
            return response()->json([
                'data' => array($tx),
                'page_show' => $page_show,
                'recordFiltered' => 1,
                'success' => true,
            ]);
        }



        return view('primary.transaction.trades.show', compact('tx', 'tx_related'));
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
