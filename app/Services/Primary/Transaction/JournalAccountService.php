<?php

namespace App\Services\Primary\Transaction;

use Illuminate\Support\Facades\DB;
use App\Models\Company\Finance\Account;
use App\Models\Company\Finance\JournalEntry;
use App\Models\Company\Finance\JournalEntryDetail;
use Illuminate\Http\Request;

use App\Models\Primary\Transaction;

use Yajra\DataTables\Facades\DataTables;

use Illuminate\Support\Facades\Auth;


class JournalAccountService
{
    public function addJournalEntry($data, $details = [])
    {
        $player = Auth::user()->player;
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


    public function getQueryData(Request $request){
        $space_id = get_space_id($request);

        $query = Transaction::with('input', 'type', 'details', 'details.detail', 'details.detail.item')
                            ->where('model_type', 'JE')
                            ->where('space_type', 'SPACE')
                            ->where('space_id', $space_id);

        return $query;
    }


    public function getJournalDT(Request $request){
        $query = $this->getQueryData($request);


        // Limit
        $limit = $request->get('limit');
        if($limit){
            if($limit != 'all'){
                $query->limit($limit);
            }
        } else {
            $query->limit(50);
        }

        
        // Search
        $keyword = $request->get('q');
        if($keyword){
            $query->where(function($q) use ($keyword){
                $q->where('sent_time', 'like', "%{$keyword}%")
                ->orWhere('number', 'like', "%{$keyword}%")
                ->orWhere('sender_notes', 'like', "%{$keyword}%")
                ->orWhereHas('details.detail', function ($q2) use ($keyword) {
                    $q2->where('notes', 'like', "%{$keyword}%");
                });
            });
        }


        return DataTables::of($query)
            ->make(true);
    }
}
