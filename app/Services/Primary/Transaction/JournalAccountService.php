<?php

namespace App\Services\Primary\Transaction;

use Illuminate\Support\Facades\DB;
use App\Models\Company\Finance\Account;
use App\Models\Company\Finance\JournalEntry;
use App\Models\Company\Finance\JournalEntryDetail;

use App\Models\Primary\Transaction;

class JournalAccountService
{
    public function addJournalEntry($data, $details = [])
    {
        $player = auth()->user()->player;
        $space_id = session('space_id') ?? null;

        $tx_ja = Transaction::create([
            'space_type' => 'SPACE',
            'space_id' => $data['space_id'] ?? $space_id,
            'model_type' => 'JE',
            'sender_type' => $data['sender_type'] ?? 'PLAY',
            'sender_id' => $data['sender_id'] ?? $player->id,
            'input_type' => $data['input_type'] ?? null,
            'input_id' => $data['input_id'] ?? null,
            'sent_time' => $data['sent_time'] ?? Date('Y-m-d'),
            'sender_notes' => $data['sender_notes'] ?? null,
            'total' => $data['total'] ?? 0,
        ]);

        $journal_details = [];
        foreach ($details as $detail) {
            $journal_details[] = [
                'transaction_id' => $tx_ja->id,
                'detail_type' => 'IVT',
                'detail_id' => $detail['account_id'],
                'debit' => $detail['debit'] ?? 0,
                'credit' => $detail['credit'] ?? 0,
                'notes' => $detail['notes'] ?? null,
            ];
        }

        $tx_ja->details()->createMany($journal_details);

        // post journal to GL
        // $tx_ja->postJournalEntrytoGeneralLedger();

        $tx_ja->generateNumber();
        $tx_ja->save();

        return $tx_ja;
    }

    public function updateJournalEntry($tx, $data, $details = [])
    {
        // Update main journal entry
        $tx->update($data);

        // Delete old details
        $tx->details()->delete();

        // Create new details
        $journalDetails = [];
        foreach ($details as $detail) {
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

        $tx->save();
    }
}
