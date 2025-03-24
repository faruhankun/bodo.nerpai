<?php

namespace App\Models\Store;

use App\Models\Store\StoreInboundProduct;
use App\Models\Store\StoreInventory;
use App\Models\Store\StoreOutboundItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StoreLocation extends Model
{
	protected $table = 'store_locations';

	protected $fillable = [
		'store_id',
		'room',
		'rack',
		'notes',
	];

	public function store()
	{
		return $this->belongsTo(Store::class);
	}

	public function storeInventories(): HasMany
	{
		return $this->hasMany(StoreInventory::class);
	}

	public function storeInboundProducts(): HasMany
	{
		return $this->hasMany(StoreInboundProduct::class);
	}

	public function print_location()
	{
		return $this->room . ' : ' . $this->rack;
	}
}
