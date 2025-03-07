<?php

namespace App\Models\Store;

use App\Models\WarehouseLocation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreOutboundProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'store_outbound_products';
    public $timestamps = true;

    protected $fillable = [
        'store_outbound_id',
        'store_product_id',
        'warehouse_location_id',
        'quantity',
        'notes'
    ];

    public function storeOutbound(): BelongsTo
    {
        return $this->belongsTo(StoreOutbound::class);
    }

    public function storeProduct(): BelongsTo
    {
        return $this->belongsTo(StoreProduct::class);
    }

    public function warehouseLocation(): BelongsTo
    {
        return $this->belongsTo(WarehouseLocation::class);
    }
}
