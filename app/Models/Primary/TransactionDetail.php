<?php

namespace App\Models\Primary;

use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    protected $table = 'transaction_details';

    public $timestamps = true;

    protected $fillable = [
        'transaction_id',
        'type_type',
        'type_id',
        'quantity',
        'price',
        'cost_per_unit',
        'data',
        'notes',
    ];



    // Relationships
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function type()
    {
        return $this->morphTo();
    }
}