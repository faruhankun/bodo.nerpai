<?php

namespace App\Models\Store;

use App\Models\Company\Store;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StorePos extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'store_pos';
    public $timestamps = true;

    protected $fillable = [
        'number',
        'store_id',
        'store_customer_id',
        'store_employee_id',
        'date',
        'total_amount',
        'tax_amount',
        'payment_method',
        'payment_amount',
        'status',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'date' => 'date',
    ];

    public function generatePosNumber(): string
    {
        $this->number = 'POS_' . $this->date->format('Y-m-d') . '_' . $this->id;
        return $this->number;
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function store_customer(): BelongsTo
    {
        return $this->belongsTo(StoreCustomer::class);
    }

    public function store_employee(): BelongsTo
    {
        return $this->belongsTo(StoreEmployee::class);
    }

    public function store_pos_products(): HasMany
    {
        return $this->hasMany(StorePosProduct::class);
    }
}
