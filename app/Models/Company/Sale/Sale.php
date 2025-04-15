<?php

namespace App\Models\Company\Sale;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\Company\Product;
use App\Models\Employee;

use App\Models\Company\Warehouse;
use App\Models\Company\Customer;
use App\Models\Company\Courier;
use App\Models\Company\Shipment;

use App\Models\Company\Sale\SaleItems;
use App\Models\Company\Sale\SaleInvoice;

use App\Models\Warehouse\Outbound;

class Sale extends Model
{
    protected $table = 'sales';

    use HasFactory;

    protected $fillable = [
        'number',
        'date',
        'employee_id',
        'customer_id',
        'consignee_type',
        'consignee_id',
        'warehouse_id',
        'total_amount',
        'status',
        'customer_notes',
        'admin_notes',
        'courier_id',
        'estimated_shipping_fee',
        'shipping_fee_discount',
    ];

    public function generateSoNumber(): string
    {
        $this->number = 'SO_' . $this->date . '_' . $this->id;
        return $this->number;
    }


    // relations
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    
    public function items(): HasMany
    {
        return $this->hasMany(SaleItems::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function consignee()
    {
        return $this->morphTo();
    }

    public function courier()
    {
        return $this->belongsTo(Courier::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function invoices()
    {
        return $this->hasMany(SaleInvoice::class);
    }


    public function shipments()
    {
        return $this->hasManyThrough(
            Shipment::class,
            Outbound::class,
            'source_id', // Foreign key di `outbounds` yang mengarah ke `sales.id`
            'transaction_id', // Foreign key di `shipments` yang mengarah ke `outbounds.id`
            'id', // Local key di `sales`
            'id' // Local key di `outbounds`
        )->where('outbounds.source_type', 'SO')
         ->where('shipments.transaction_type', 'OUTB');
    }


    public function outbounds(): HasMany
    {
        return $this->hasMany(Outbound::class, 'source_id', 'id')
                    ->where('source_type', 'SO');
    }
}
