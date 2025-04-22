<?php

namespace App\Models\Primary;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use SoftDeletes;

    protected $table = 'inventories';

    public $timestamps = true;

    protected $fillable = [
        'code',
        'model_type',
        'model_id',
        'parent_type',
        'parent_id',
        'type_type',
        'type_id',
        'space_type',
        'space_id',
        'location_type',
        'location_id',
        'item_type',
        'item_id',
        'name',
        'expiry_date',
        'quantity',
        'balance',
        'cost_per_unit',
    ];



    // morph
    public function parent()
    {
        return $this->morphTo();
    }
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


    // model
}