<?php

namespace App\Models\Company\Sale;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Company\Sale\Sale;

use App\Models\Company\Finance\Receivable;

class SaleInvoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sale_id',
        'number',
        'date',
        'due_date',
        'cost_products',
        'vat_input',
        'cost_packing',
        'cost_insurance',
        'cost_freight',
        'total_amount',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'due_date' => 'date',
        'total_amount' => 'decimal:2',
    ];


    public function generateNumber(): string
    {
        $this->number = 'INV_' . $this->id . '_' . $this->sale->number;
        return $this->number;
    }



    // relations 

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function receivable(): HasOne
    {
        return $this->hasOne(Receivable::class);
    }
}
