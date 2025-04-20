<?php

namespace App\Models\Primary;

use Illuminate\Database\Eloquent\Model;

class Relation extends Model
{
    protected $table = 'relations';

    protected $fillable = [
        'model1_type',
        'model1_id',
        'model1_quantity',
        'model2_type',
        'model2_id',
        'model2_quantity',
        'name',
        'type',
        'status',
        'notes',
    ];



    // relations
    public function model1()
    {
        return $this->morphTo();
    }

    public function model2()
    {
        return $this->morphTo();
    }
}
