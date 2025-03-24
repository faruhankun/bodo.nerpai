<?php

namespace App\Models\Company\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Company\Sale\SaleInvoice;
use App\Models\Company\Customer;

class Receivable extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sale_invoice_id',
        'customer_id',
        'total_amount',
        'balance',
        'status',
        'notes'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(SaleInvoice::class, 'sale_invoice_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
