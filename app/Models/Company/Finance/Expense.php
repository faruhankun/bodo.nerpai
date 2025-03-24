<?php

namespace App\Models\Company\Finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Employee;

class Expense extends Model
{
    use SoftDeletes;

    protected $table = 'expenses';

    public $timestamps = true;

    protected $fillable = [
        'account_id',
        'number',
        'date',
        'requested_by',
        'approved_by',
        'type',
        'description',
        'amount',
        'payment_method',
        'consignee_type',
        'consignee_id',
        'status',
        'notes',
    ];

    public static $types = [
        'operational',
        'logistics',
        'marketing',
        'payroll',
        'maintenance',
        'rental',
        'utility',
        'tax',
        'legal',
        'other',
    ];


    protected $casts = [
        'date' => 'date',
    ];

    //accessors
    public static function get_types()
    {
        return self::$types;
    }


    public function generateNumber()
    {
        $this->number = 'EXP_' . $this->type . '_' . $this->id;
        return $this->number;
    }


    //relations
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function consignee()
    {
        return $this->morphTo();
    }

    public function requestedby()
    {
        return $this->belongsTo(Employee::class, 'requested_by');
    }

    public function approvedby()
    {
        return $this->belongsTo(Employee::class, 'approved_by');
    }
}
