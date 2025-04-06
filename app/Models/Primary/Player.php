<?php

namespace App\Models\Primary;

use Illuminate\Database\Eloquent\Model;

use App\Models\User;

class Player extends Model
{
    protected $connection = 'primary';
    
    protected $table = 'players';

    public $timestamps = true;

    protected $fillable = [
        'code',
        'type_type',
        'type_id',
        'size_type',
        'size_id',
        'name',
        'address',
        'status',
        'notes',
    ];


    // relations
    public function size()
    {
        return $this->morphTo();
    }

    public function type()
    {
        return $this->morphTo();
    }


    public function user(): hasOne
    {
        return $this->hasOne(User::class, 'player_id', 'id');
    }
}