<?php

namespace App\Models\Primary;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Item extends Model
{
    use SoftDeletes;

    protected $table = 'items';

    public $timestamps = true;

    protected $fillable = [
        'primary_code',
        'code',
        'sku',
        'parent_type',
        'parent_id',
        'type_type',
        'type_id',
        'model_type',
        'model_id',
        'name',
        'price',
        'cost',
        'weight',
        'dimension',
        'status',
        'notes',

        'space_type',
        'space_id',

        'description',
        'files',
    ];



    // Relations
    public function type()
    {
        return $this->morphTo();
    }


    public function parent()
    {
        return $this->morphTo();
    }


    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }


    public function space()
    {
        return $this->morphTo();
    }


    public function transaction_details()
    {
        return $this->hasMany(TransactionDetail::class, 'detail_id', 'id')
                    ->where('detail_type', 'ITM')
                    ->orderBy('id', 'desc');
    }


    public function transactions()
    {
        return $this->belongsToMany(Transaction::class, 'transaction_details', 'detail_id', 'transaction_id')
                    ->wherePivot('detail_type', 'ITM')
                    ->where('transactions.model_type', 'TRD')
                    ->where('transaction_details.detail_type', 'ITM')
                    ->where('transaction_details.deleted_at', null);
    }
}