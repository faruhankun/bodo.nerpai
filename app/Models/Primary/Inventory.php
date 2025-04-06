<?php

namespace App\Models\Primary;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventories';

    public $timestamps = true;

    protected $fillable = [
        'code',
        'type_type',
        'type_id',
        'space_type',
        'space_id',
        'location_type',
        'location_id',
        'item_type',
        'item_id',
        'expiry_date',
        'quantity',
        'balance',
        'cost_per_unit',
    ];



    // morph
    public function type()
    {
        return $this->morphTo();
    }

    public function space()
    {
        return $this->morphTo();
    }

    public function location()
    {
        return $this->morphTo();
    }

    public function item()
    {
        return $this->morphTo();
    }
}