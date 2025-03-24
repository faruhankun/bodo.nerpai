<?php

namespace App\Models\Company;

use App\Models\Store\StoreCustomer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\Company\Finance\Receivable;

class Customer extends Model
{
    protected $connection = 'tenant';

    use HasFactory;

    public $timestamps = true;

    protected $table = 'customers';

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'address',
        'status',
        'notes',
        'entity_type',
        'entity_id',
    ];

    
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function storeCustomers()
    {
        return $this->hasMany(StoreCustomer::class);
    }

    public function receivables()
    {
        return $this->hasMany(Receivable::class);
    }

    public function entity()
    {
        return $this->morphTo();
    }
}
