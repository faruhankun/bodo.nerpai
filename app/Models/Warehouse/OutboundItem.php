<?php

namespace App\Models\Warehouse;

use Illuminate\Database\Eloquent\Model;

use App\Models\Company\Product;
use App\Models\Company\WarehouseLocation;
use App\Models\Warehouse\Outbound;
use App\Models\Company\Inventory\Inventory;

class OutboundItem extends Model
{
	protected $fillable = [
		'outbound_id',
		'inventory_id',
		'warehouse_location_id',
		'quantity',
		'cost_per_unit',
		'total_cost',
		'notes',
	];




	// Relationships
	public function outbound() {
		return $this->belongsTo(Outbound::class);
	}

	public function item() {
		return $this->belongsTo(Inventory::class, 'inventory_id');
	}

	public function inventory() {
		return $this->belongsTo(Inventory::class);
	}

	public function warehouse_location() {
		return $this->belongsTo(WarehouseLocation::class);
	}
}
