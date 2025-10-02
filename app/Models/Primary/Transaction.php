<?php

namespace App\Models\Primary;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Primary\TransactionDetail;



class Transaction extends Model
{
    use SoftDeletes;

    protected $table = 'transactions';

    public $timestamps = true;

    protected $fillable = [
        'number',
        'class',

        'space_type',
        'space_id',
        'model_type',
        'model_id',

        'type_type',
        'type_id',
        'input_type',
        'input_id',
        'output_type',
        'output_id',

        'relation_type',
        'relation_id',

        'parent_type',
        'parent_id',

        'sender_type',
        'sender_id',
        'receiver_type',
        'receiver_id',
        'handler_type',
        'handler_id',

        'input_address',
        'output_address',

        'sent_time',
        'sent_date',
        'received_date',
        'received_time',
        'handler_number',
        
        'total',
        'fee',
        'fee_rules',

        'description',
        'sender_notes',
        'receiver_notes',
        'handler_notes',

        'status',
        'notes',

        'files',
        'tags',
        'links',
    ];

    protected $casts = [
        'sent_time' => 'datetime',
        'received_time' => 'datetime',
        'files' => 'json',
        'tags' => 'json',
        'links' => 'json',
    ];



    // function
    public function generateNumber()
    {
        $this->number = ($this->type_type ?? 'TX') . '_' . $this->id;
        return $this->number;
    }


    // Relationships
    public function space()
    {
        return $this->morphTo();
    }

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

    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }



    public function outputs()
    {
        return $this->hasMany(Transaction::class, 'input_id')
            ->where('input_type', 'TX');
    }



    public function relation()
    {
        return $this->morphTo();
    }


    public function relations()
    {
        return $this->hasMany(Transaction::class, 'relation_id', 'id')
            ->where('relation_type', 'TX');
    }


    public function parent()
    {
        return $this->morphTo();
    }


    public function children()
    {
        return $this->hasMany(Transaction::class, 'parent_id', 'id');
    }
}