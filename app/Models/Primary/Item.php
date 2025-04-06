<?php

namespace App\Models\Primary;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
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
}