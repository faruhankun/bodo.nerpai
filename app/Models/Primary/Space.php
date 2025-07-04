<?php

namespace App\Models\Primary;

use Illuminate\Database\Eloquent\Model;

use App\Models\Primary\Access\Variable;

use Illuminate\Database\Eloquent\SoftDeletes;

class Space extends Model
{
    use softDeletes;

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
    public function spaceAndChildren()
    {
        return $this->allChildren()->push($this);
    }


    public function allChildren()
    {
        $all = collect();

        foreach ($this->children as $child) {
            $all->push($child);
            $all = $all->merge($child->allChildren());
        }

        return $all->unique('id')->values();
    }

    
    public function allParents()
    {
        $all = collect();

        $parent = $this->parent;

        if ($parent) {
            $all->push($parent);
            $all = $all->merge($parent->allParents());
        }

        return $all->unique('id')->values();
    }


    public function players()
    {
        return $this->belongsToMany(Player::class, 'relations', 'model1_id', 'model2_id')
                    ->where('relations.model2_type', 'PLAY')
                    ->where('relations.model1_type', 'SPACE')
                    ->withPivot('name', 'type', 'status', 'notes')
                    ->with(['size', 'type'])
                    ->withTimestamps();
    }


    public function variables() 
    {
        return $this->hasMany(Variable::class)
                    ->where('space_type', 'SPACE');
    }
}