<?php

namespace App\Models\Primary;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use SoftDeletes;
    
    protected $table = 'groups';

    protected $fillable = [
        'code',
        'name',
        'address',
        'status',
        'notes',
    ];

    protected $casts = [
        'address' => 'json',
    ];


    // relations
    public function player()
    {
        return $this->morphOne(Player::class, 'size');
    }
}
