<?php

namespace App\Models\Primary;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

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


    public function spaces()
    {
        return $this->belongsToMany(Space::class, 'relations', 'model2_id', 'model1_id')
                    ->where('relations.model1_type', 'SPACE')
                    ->where('relations.model2_type', 'PLAY')
                    ->withPivot('name', 'type', 'status', 'notes');
    }
}