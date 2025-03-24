<?php

namespace App\Models\Company\Inventory;

use Illuminate\Database\Eloquent\Model;

use App\Models\Company\Inventory\InventoryTransfer;

class InventoryTransferItem extends Model
{
    protected $table = 'inventory_transfer_items';

    public $timestamps = true;

    protected $fillable = [
        'inventory_transfer_id',
        'item_type',
        'item_id',
        'quantity',
        'cost_per_unit',
        'total_cost',
        'notes',
    ];



    // relations
    public function inventory_transfer()
    {
        return $this->belongsTo(InventoryTransfer::class);
    }

    public function item()
    {
        return $this->morphTo();
    }
}
