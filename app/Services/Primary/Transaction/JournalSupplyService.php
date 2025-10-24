<?php

namespace App\Services\Primary\Transaction;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

use Yajra\DataTables\Facades\DataTables;

use App\Models\Primary\Transaction;
use App\Models\Primary\Player;
use App\Models\Primary\Inventory;
use App\Models\Primary\TransactionDetail;
use App\Models\Primary\Space;



class JournalSupplyService
{
    public $status_types = [
        'TX_READY' => 'PERLU DIKIRIM',
        'TX_COMPLETED' => 'PESANAN SELESAI',
    ];



    public function addJournal($data, $details = [])
    {
        $player_id = $data['sender_id'] ?? auth()->user()->player->id;
        $space_id = $data['space_id'] ?? null;
        if(is_null($space_id)){
            abort(403);
        }

        $tx = Transaction::create([
            'space_type' => 'SPACE',
            'space_id' => $data['space_id'] ?? $space_id,
            'model_type' => 'JS',
            'sender_type' => $data['sender_type'] ?? 'PLAY',
            'sender_id' => $data['sender_id'] ?? $player_id,
            'input_type' => $data['input_type'] ?? null,
            'input_id' => $data['input_id'] ?? null,
            'sent_time' => $data['sent_time'] ?? Date('Y-m-d'),
            'sender_notes' => $data['sender_notes'] ?? null,
            'total' => $data['total'] ?? 0,

            'status' => 'TX_READY',
        ]);

        $journal_details = [];
        foreach ($details as $detail) {
            $journal_details[] = [
                'transaction_id' => $tx->id,
                'detail_type' => 'IVT',
                'detail_id' => $detail['ivt_id'],
                'debit' => $detail['debit'] ?? 0,
                'credit' => $detail['credit'] ?? 0,
                'notes' => $detail['notes'] ?? null,
            ];
        }

        $tx->details()->createMany($journal_details);

        $tx->generateNumber();
        $tx->save();

        return $tx;
    }


    
    public function updateJournal($tx, $data, $details = [])
    {
        // Update main journal entry
        $tx->update($data);

        // Delete old details
        $old_details_ids = collect($tx->details)->pluck('detail_id')->unique();
        $tx->details()->delete();

        // Create new details
        $journalDetails = [];
        $balance_change = 0;
        foreach ($details as $detail) {
            // inventory_id
            $detail['quantity'] = $detail['quantity'] ?? 0;

            $detail['detail_id'] = $detail['detail_id'] ?? null;
            $detail['debit'] = $detail['quantity'] >= 0 ? $detail['quantity'] : 0;
            $detail['credit'] = $detail['quantity'] < 0 ? abs($detail['quantity']) : 0;
            $detail['notes'] = $detail['notes'] ?? null;
            $detail['model_type'] = $detail['model_type'] ?? 'UNDF';
            $detail['cost_per_unit'] = $detail['cost_per_unit'] ?? 0;

            $journalDetails[] = [
                'transaction_id' => $tx->id,
                'detail_type' => 'IVT',
                'detail_id' => $detail['detail_id'],
                'debit' => $detail['debit'],
                'credit' => $detail['credit'],
                'notes' => $detail['notes'] ?? null,
                'quantity' => $detail['quantity'],
                'model_type' => $detail['model_type'],
                'cost_per_unit' => $detail['cost_per_unit'],
            ];

            $balance_change += $detail['quantity'] * $detail['cost_per_unit'];
        }

        $tx->details()->createMany($journalDetails);

        if(is_null($tx->number))
            $tx->generateNumber();
        $tx->total = $balance_change;
        $tx->save();



        // Update Balance
        $supply_ids = collect($details)->pluck('detail_id')->unique();
        $supply_ids = $supply_ids->merge($old_details_ids)->unique();
        foreach($supply_ids as $supply_id) {
            $supply = Inventory::find($supply_id);
            $supply->updateSupplyBalance();
        }


        return $tx;
    }


    public function mirrorJournal($tx, $space_id)
    {
        $player_id = $tx->handler_id ?? auth()->user()->player->id;

        if(!$tx->input){
            $data = [
                'space_id' => $space_id,
                'sender_id' => $player_id,
                'sent_time' => $tx->sent_time ?? now(),
                'sender_notes' => $tx->handler_notes . 
                                " request: " . $tx->space?->name .
                                " by: " . $tx->space?->name,
            ];

            $input_tx = $this->addJournal($data);
            $tx->input_type = 'TX';
            $tx->input_id = $input_tx->id;
            $tx->status = 'TX_READY';
        }

        $tx->save();
        $this->mirrorJournalToChildren($tx->id);

        return $tx;
    }


    public function mirrorJournalToChildren($tx_id)
    {
        try {
            $tx = Transaction::with('outputs', 'input')->findOrFail($tx_id);

            $tx_related = $tx->outputs ?? [];
            if($tx->input){
                $tx_related[] = $tx->input;
            }

            foreach($tx_related as $child){
                $data = [
                    'sent_time' => $tx->sent_time ?? now(),
                    'sender_notes' => $tx->handler_notes . 
                                    " request: " . $tx->space?->name .
                                    " by: " . $tx->sender?->name,
                    'total' => $tx->total,
                ];


                // details
                $details = [];
                foreach($tx->details as $detail){
                    $child_detail = Inventory::where('space_id', $child->space_id)
                                    ->where('model_type', 'SUP')
                                    ->where('item_id', $detail->detail->item_id)
                                    ->first();

                    if(!$child_detail){
                        $child_detail = Inventory::create([
                            'space_type' => 'SPACE',
                            'space_id' => $child->space_id,
                            'model_type' => 'SUP',
                            'item_type' => 'ITM',
                            'item_id' => $detail->detail->item_id,
                            'name' => $detail->detail->item->name,
                            'sku' => $detail->detail->item->sku,
                            'code' => $detail->detail->item->code,
                        ]);
                    }


                    $details[] = [
                        'detail_id' => $child_detail->id,
                        'quantity' => ($detail->debit - $detail->credit) * -1,  // karena mirror
                        'model_type' => $detail->model_type,
                        'cost_per_unit' => $detail->cost_per_unit,
                        'notes' => $detail->notes,
                    ];
                }

                $this->updateJournal($child, $data, $details);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'success' => false], 404);
        }
    }


    public function updateSupply($details = [])
    {
        $detail_ids = collect($details)->pluck('detail_id')->unique();
        foreach($detail_ids as $detail_id) {
            $supply = Inventory::find($detail_id);
            $supply->updateSupplyBalance();
        }
    }



    public function getData(Request $request)
    {
        $query = $this->getQueryData($request);       

        $query = $query->orderBy('transactions.id', 'desc');


        return DataTables::of($query)
            ->addColumn('actions', function ($data) {
                $route = 'journal_supplies';

                $actions = [
                    'show' => 'modal',
                    'show_modal' => 'primary.transaction.journal_supplies.show_modal',
                    'edit' => 'button',
                    'delete' => 'button',
                ];


                // jika punya input atau outputs, maka tidak bisa dihapus
                if($data->outputs->isNotEmpty() || $data->input){
                    unset($actions['delete']);
                }


                // jika statusnya TX_COMPLETED, maka tidak bisa dihapus
                if($data->status == 'TX_COMPLETED'){
                    // unset($actions['edit']);
                    unset($actions['delete']);
                }


                return view('components.crud.partials.actions', compact('data', 'route', 'actions'))->render();
            })

            ->addColumn('sku', function ($data){
                return $data->details->map(function ($detail){
                    return $detail->detail->sku;
                })->implode(', ');
            })

            ->addColumn('all_notes', function ($data){
                return $data->sender_notes . '<br>' . $data->handler_notes;
            })

            ->addColumn('data', function ($data) {
                return $data;
            })

            ->filter(function ($query) use ($request) {                                  
                if ($request->has('search') && $request->search['value'] || $request->filled('q')) {
                    $search = $request->search['value'] ?? $request->q;

                    $query = $query->where(function ($q) use ($search) {
                        $q->where('transactions.id', 'like', "%{$search}%")
                            ->orWhere('transactions.sent_time', 'like', "%{$search}%")
                            ->orWhere('transactions.number', 'like', "%{$search}%")
                            ->orWhere('transactions.sender_notes', 'like', "%{$search}%")
                            ->orWhere('transactions.handler_notes', 'like', "%{$search}%");

                        $q->orWhereHas('details', function ($q2) use ($search) {
                            $q2->where('transaction_details.notes', 'like', "%{$search}%")
                                ->orWhere('transaction_details.model_type', 'like', "%{$search}%")
                            ;
                        });

                        $q->orWhereHas('details.detail', function ($q3) use ($search) {
                            $q3->where('name', 'like', "%{$search}%")
                                ->orWhere('sku', "{$search}")
                            ;
                        });
                    });
                }    
            })

            ->rawColumns(['actions', 'all_notes'])
            ->make(true);
    }

    
    public function getQueryData(Request $request){
        $space_id = get_space_id($request);

        $space = Space::findOrFail($space_id);
        $space_ids = $space->spaceAndChildren()->pluck('id')->toArray() ?? [];
        $space_ids = array_merge($space_ids, [$space_id]);

        $query = Transaction::with('input', 'type', 'details', 'details.detail', 'details.detail.item', 'space')
            ->where('model_type', 'JS')
            ->orderBy('sent_time', 'desc');

        $query = $query->where('transactions.space_type', 'SPACE');



        // filter space
        $space_select = $request->get('space_select') ?? 'all';
        if($space_select == 'exc'){
            $space_select_options = $this->status_types;

            $query->whereNotIn('status', collect($space_select_options)->keys()->toArray());
        } else if($space_select != 'all'){
            $query->where('transactions.space_id', $space_select);
        } else if($space_select == 'all'){
            $query->whereIn('transactions.space_id', $space_ids);
        }



        // Limit
        $limit = $request->get('limit');
        if($limit){
            if($limit != 'all'){
                $query->limit($limit);
            } 
        } else {
            $query->limit(50);
        }                



        // filter status
        $status_select = $request->get('status_select') ?? 'all';
        if($status_select == 'exc'){
            $status_select_options = $this->status_types;

            $query->whereNotIn('status', collect($status_select_options)->keys()->toArray());
        } else if($status_select != 'all'){
            $query->where('status', $status_select);
        }
        


        return $query;
    }
}
