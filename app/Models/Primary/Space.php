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

    public function children()
    {
        return $this->hasMany(Space::class, 'parent_id')
                    ->where('parent_type', 'SPACE');
    }


    // Relations
    public function players()
    {
        return $this->belongsToMany(Player::class, 'relations', 'model1_id', 'model2_id')
                    ->where('relations.model2_type', 'PLAY')
                    ->where('relations.model1_type', 'SPACE')
                    ->withPivot('name', 'type', 'status', 'notes')
                    ->with(['size', 'type'])
                    ->withTimestamps();
    }
}