<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Company\Finance\Payable;

class PurchaseInvoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'purchase_id',
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

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function generateInvoiceNumber(): string
    {
        $this->number = 'INV_' . $this->id . '_' . $this->purchase->po_number;
        return $this->number;
    }

    public function payable(): HasOne
    {
        return $this->hasOne(Payable::class);
    }
}
