<?php

namespace App\Models\Company;

use App\Models\Store\StoreInboundProduct;
use App\Models\Store\StoreInventory;
use App\Models\Store\StoreOutboundItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WarehouseLocation extends Model
{
	protected $table = 'warehouse_locations';

	protected $fillable = [
		'warehouse_id',
		'room',
		'rack'
	];

	public function warehouse()
	{
		return $this->belongsTo(Warehouse::class);
	}

	public function inventory()
	{
		return $this->hasMany(Inventory::class);
	}

	public function storeInventories(): HasMany
	{
		return $this->hasMany(StoreInventory::class);
	}

	public function storeInboundProducts(): HasMany
	{
		return $this->hasMany(StoreInboundProduct::class);
	}

	public function storeOutboundItems(): HasMany
	{
		return $this->hasMany(StoreOutboundItem::class);
	}


	public function print_location()
	{
		return $this->room . ' : ' . $this->rack;
	}
}
