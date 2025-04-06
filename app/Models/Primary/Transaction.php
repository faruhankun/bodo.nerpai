<?php

namespace App\Models\Primary;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    protected $table = 'transactions';

    public $timestamps = true;

    protected $fillable = [
        'number',
        'class',

        'type_type',
        'type_id',
        'input_type',
        'input_id',
        'output_type',
        'output_id',

        'sender_type',
        'sender_id',
        'receiver_type',
        'receiver_id',
        'handler_type',
        'handler_id',

        'input_address',
        'output_address',

        'sent_date',
        'received_date',
        'handler_number',
        
        'total',
        'fee',
        'fee_rules',

        'sender_notes',
        'receiver_notes',
        'handler_notes',

        'status',
    ];



    // Relationships
    public function type()
    {
        return $this->morphTo();
    }

    public function input()
    {
        return $this->morphTo();
    }

    public function output()
    {
        return $this->morphTo();
    }

    public function sender()
    {
        return $this->morphTo();
    }

    public function receiver()
    {
        return $this->morphTo();
    }

    public function handler()
    {
        return $this->morphTo();
    }
}