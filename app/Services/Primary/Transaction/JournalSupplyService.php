<?php

namespace App\Services\Primary\Transaction;

use Illuminate\Support\Facades\DB;

use App\Models\Primary\Transaction;
use App\Models\Primary\Player;
use App\Models\Primary\Inventory;

class JournalSupplyService
{
    public function addJournal($data, $details = [])
    {
        $player_id = $data['sender_id'] ?? auth()->user()->player->id;
        $space_id = $data['space_id'] ?? (session('space_id') ?? null);

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
        }

        $tx->save();
        $this->mirrorJournalToChildren($tx->id);

        return $tx;
    }


    public function mirrorJournalToChildren($tx_id)
    {
        try {
            $tx = Transaction::with('children', 'input')->findOrFail($tx_id);

            $tx_related = $tx->children ?? [];
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
}
