<?php

namespace App\Models\Primary;

use Illuminate\Database\Eloquent\Model;

class Space extends Model
{
    protected $table = 'spaces';

    protected $fillable = [
        'code',
        'parent_type',
        'parent_id',
        'type_type',
        'type_id',
        'name',
        'address',
        'status',
        'notes',
    ];


    // Relations
    public function type()
    {
        return $this->morphTo();
    }


    public function parent()
    {
        return $this->morphTo();
    }
}