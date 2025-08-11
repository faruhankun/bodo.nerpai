<?php

namespace App\Models\Primary;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionDetail extends Model
{
    use softDeletes;

    protected $table = 'transaction_details';

    public $timestamps = true;

    protected $fillable = [
        'transaction_id',
        'detail_type',
        'detail_id',
        'type_type',
        'type_id',
        'model_type',
        'model_id',
        'quantity',
        'price',
        'discount',
        'weight',
        'cost_per_unit',
        'data',
        
        'sku',
        'name',
        'code',

        'debit',
        'credit',
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

    public function detail()
    {
        return $this->morphTo();
    }
}