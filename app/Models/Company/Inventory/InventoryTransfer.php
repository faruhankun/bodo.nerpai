<?php

namespace App\Models\Company\Inventory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use App\Models\Employee;
use App\Models\Company\Courier;
use App\Models\Company\Product;
use App\Models\Warehouse\Outbound;

use App\Models\Company\Inventory\InventoryTransferItem;

class InventoryTransfer extends Model
{
    protected $table = 'inventory_transfers';

    use softDeletes;

    protected $fillable = [
        'number',
        'date',
        'shipper_type',
        'shipper_id',
        'consignee_type',
        'consignee_id',
        'origin_address',
        'destination_address',
        'courier_id',
        'admin_id',
        'team_id',
        'admin_notes',
        'team_notes',
        'status',
    ];

    protected $casts = [
        'origin_address' => 'array',
        'destination_address' => 'array',
        'date' => 'date',
    ];

    public function generateNumber()
    {
        $this->number = 'ITF_' . $this->date->format('Y-m-d') . '_' . $this->id;
        return $this->number;
    }



    // relationship
    public function shipper()
    {
        return $this->morphTo();
    }

    public function consignee()
    {
        return $this->morphTo();
    }

    public function courier()
    {
        return $this->belongsTo(Courier::class);
    }

    public function admin()
    {
        return $this->belongsTo(Employee::class, 'admin_id', 'id');
    }

    public function team()
    {
        return $this->belongsTo(Employee::class, 'team_id', 'id');
    }

    public function items() :HasMany
    {
        return $this->hasMany(InventoryTransferItem::class);
    }

    public function outbounds() :HasMany
    {
        return $this->hasMany(Outbound::class, 'source_id', 'id');
    }
}
