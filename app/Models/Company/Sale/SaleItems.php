<?php

namespace App\Models\Company\Sale;

use Illuminate\Database\Eloquent\Model;

// models
use App\Models\Company\Inventory\Inventory;
use App\Models\Company\Sale\Sale;

class SaleItems extends Model
{
    protected $table = 'sale_items';

    public $timestamps = true;

    protected $fillable = [
        'sale_id',
        'item_type',
        'item_id',
        'inventory_id',
        'quantity',
        'price',
        'discount',
        'subtotal',
        'cost_per_unit',
        'total_cost',
        'notes',
    ];


    
    // relations
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function item()
    {
        return $this->morphTo();
    }
}
