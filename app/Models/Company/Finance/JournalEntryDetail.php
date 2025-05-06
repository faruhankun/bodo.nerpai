<?php

namespace App\Models\Company\Finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class JournalEntryDetail extends Model
{
    use SoftDeletes;

    protected $table = 'journal_entry_details';
    public $timestamps = true;

    protected $fillable = [
        'journal_entry_id',
        'account_id',
        'debit',
        'credit',
        'notes'
    ];

    public function journal_entry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
