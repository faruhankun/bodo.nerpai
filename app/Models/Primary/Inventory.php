<?php

namespace App\Models\Primary;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Inventory extends Model
{
    use SoftDeletes;

    protected $table = 'inventories';

    public $timestamps = true;

    protected $fillable = [
        'code',
        'model_type',
        'model_id',
        'parent_type',
        'parent_id',
        'type_type',
        'type_id',
        'space_type',
        'space_id',
        'location_type',
        'location_id',
        'item_type',
        'item_id',
        'name',
        'expiry_date',
        'quantity',
        'balance',
        'cost_per_unit',
    ];



    // morph
    public function parent()
    {
        return $this->morphTo();
    }
    public function type()
    {
        return $this->morphTo();
    }

    public function space()
    {
        return $this->morphTo();
    }

    public function location()
    {
        return $this->morphTo();
    }

    public function item()
    {
        return $this->morphTo();
    }


    // model
    // Account
    public function tx_details()
    {
        return $this->hasMany(TransactionDetail::class, 'detail_id');
    }

    public function getAccountBalance($start_date = null, $end_date = null)
    {
        if ($start_date == null) {
            $start_date = now()->startOfYear();
        }
        $start_date = Carbon::parse($start_date)->startOfDay();
    
        if ($end_date == null) {
            $end_date = now()->endOfYear();
        }
        $end_date = Carbon::parse($end_date)->endOfDay();

        if(!$this->space){
            return 0;
        }

        $space_type = $this->space_type;
        $list_space_id = $this->space->allChildren()->pluck('id')->toArray();
        $list_space_id = array_merge($list_space_id, [$this->space->id]);

        $query = $this->tx_details()
            ->whereHas('transaction', function ($query) use ($space_type, $list_space_id, $start_date, $end_date){
                $query->where('space_type', $space_type)
                    ->whereIn('space_id', $list_space_id)
                    ->whereBetween('sent_time', [$start_date, $end_date]);
            });

        $debit = (clone $query)->sum('debit');
        $credit = (clone $query)->sum('credit');

        $balance = $debit - $credit;
        if($this->type){
            if($this->type->credit && $this->type->credit == '1'){
                $balance *= -1;
            }
        }
        return $balance;
    }
}