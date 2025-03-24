<?php

namespace App\Models\Store;

use App\Models\Company\Product;
use App\Models\Company\Store;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'store_products';
    public $timestamps = true;

    protected $fillable = [
        'store_id',
        'product_id',
        'store_price',
        'status',
        'notes'
    ];

    protected $casts = [
        'store_price' => 'decimal:2',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function storePosProducts(): HasMany
    {
        return $this->hasMany(StorePosProduct::class);
    }

    public function storeInboundProducts(): HasMany
    {
        return $this->hasMany(StoreInboundProduct::class);
    }

    public function storeOutboundItems(): HasMany
    {
        return $this->hasMany(StoreOutboundItem::class);
    }
}
