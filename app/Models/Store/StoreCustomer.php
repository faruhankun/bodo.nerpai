<?php

namespace App\Models\Store;

use App\Models\Company\Store;
use App\Models\Company\Customer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreCustomer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'store_customers';
    public $timestamps = true;

    protected $fillable = [
        'store_id',
        'customer_id',
        'status',
        'notes'
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function storePos(): HasMany
    {
        return $this->hasMany(StorePos::class);
    }
}
