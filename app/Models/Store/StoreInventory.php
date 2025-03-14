<?php

namespace App\Models\Store;

use App\Models\Company\Store;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreInventory extends Model
{
    use HasFactory;

    protected $table = 'store_inventories';
    public $timestamps = true;

    protected $fillable = [
        'store_id',
        'store_product_id',
        'store_location_id',
        'expire_date',
        'quantity',
        'reserved_quantity',
        'in_transit_quantity',
        'cost_per_unit',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function store_product(): BelongsTo
    {
        return $this->belongsTo(StoreProduct::class);
    }

    public function store_location(): BelongsTo
    {
        return $this->belongsTo(StoreLocation::class);
    } 
}
