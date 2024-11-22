<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name', 
        'sale_date', 
        'total_amount', 
        'warehouse_id', 
        'status', 
        'customer_notes', 
        'admin_notes',
		'expedition_id',
		'estimated_shipping_fee',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'sales_products')
                    ->withPivot('quantity', 'price', 'note')
                    ->withTimestamps();
    }

	// Relationship with Product
    public function salesProducts()
    {
        return $this->belongsTo(SalesProduct::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
	
	public function productQuantities()
	{
		return $this->products->mapWithKeys(function ($product) {
			return [$product->id => $product->pivot->quantity];
		});
	}


	// Get Status Name (optional helper method for status display)
    public function getStatusLabelAttribute()
    {
        return ucfirst(strtolower(str_replace('_', ' ', $this->status)));
    }
	
	
	public function expedition()
	{
		return $this->belongsTo(Expedition::class);
	}
	
	
	public function outboundRequests()
	{
		return $this->hasMany(outboundRequests::class, 'sales_order_id');
	}


    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
