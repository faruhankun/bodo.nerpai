<?php

namespace App\Models\Store;

use App\Models\Shipment;
use App\Models\Company\Store;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreOutbound extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'store_outbounds';
    public $timestamps = true;

    protected $fillable = [
        'store_id',
        'shipment_id',
        'store_employee_id',
        'notes',
        'status',
        'outbound_date'
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function storeEmployee(): BelongsTo
    {
        return $this->belongsTo(StoreEmployee::class);
    }

    public function storeOutboundItems(): HasMany
    {
        return $this->hasMany(StoreOutboundItem::class);
    }
}
