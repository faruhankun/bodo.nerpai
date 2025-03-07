<?php

namespace App\Models\Store;

use App\Models\Company\Store;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreWarehouse extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'store_warehouses';
    public $timestamps = true;

    protected $fillable = [
        'store_id',
        'warehouse_id',
        'status',
        'notes'
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
}
