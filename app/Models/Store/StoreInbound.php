<?php

namespace App\Models\Store;

use App\Models\Shipment;
use App\Models\Company\Store;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Company\ShipmentConfirmation;

class StoreInbound extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'store_inbounds';
    public $timestamps = true;

    protected $fillable = [
        'number',
        'store_id',
        'shipment_confirmation_id',
        'store_employee_id',
        'date',
        'notes',
        'status',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function store_employee(): BelongsTo
    {
        return $this->belongsTo(StoreEmployee::class);
    }

    public function store_inbound_products(): HasMany
    {
        return $this->hasMany(StoreInboundProduct::class);
    }

    public function shipment_confirmation(): BelongsTo
    {
        return $this->belongsTo(ShipmentConfirmation::class);
    }
}
