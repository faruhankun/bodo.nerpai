<?php

namespace App\Services\Primary\Transaction;

use Illuminate\Support\Facades\DB;

use App\Models\Primary\Transaction;
use App\Models\Primary\Player;

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
        foreach ($details as $detail) {
            // inventory_id
            $ivt = Inventory::where('space_id', $tx->space_id)
                                ->where('space_type', 'SPACE')
                                ->where('model_type', 'SUP')            // supply
                                ->where('item_id', $detail['item_id'])
                                ->first();

            $journalDetails[] = [
                'transaction_id' => $tx->id,
                'detail_type' => 'IVT',
                'detail_id' => $detail['detail_id'],
                'debit' => $detail['debit'],
                'credit' => $detail['credit'],
                'notes' => $detail['notes'] ?? null,
            ];
        }

        $tx->details()->createMany($journalDetails);

        if(is_null($tx->number))
            $tx->generateNumber();
        $tx->save();
    }
}
