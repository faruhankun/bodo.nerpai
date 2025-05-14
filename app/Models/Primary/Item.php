<?php

namespace App\Models\Primary;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}