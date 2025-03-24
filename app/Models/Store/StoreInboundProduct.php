<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Store\StoreLocation;

class StoreInboundProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'store_inbound_products';
    public $timestamps = true;

    protected $fillable = [
        'store_inbound_id',
        'store_product_id',
        'store_location_id',
        'quantity',
        'notes',
    ];

    public function store_inbound(): BelongsTo
    {
        return $this->belongsTo(StoreInbound::class);
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
