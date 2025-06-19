<?php

namespace App\Models\Primary;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\User;

use Illuminate\Support\Facades\DB;

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

    protected function casts(): array
    {
        return [ 
            'address' => 'json',
        ];
    }


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


    public function relatedPlayers()
    {
        $relatedIds = DB::table('relations')
                        ->where('model1_type', 'PLAY')
                        ->where('model2_type', 'PLAY')
                        ->where(function ($query) {
                            $query->where('model1_id', $this->id)
                                ->orWhere('model2_id', $this->id);
                        })
                        ->selectRaw('
                            CASE 
                                WHEN model1_id = ? THEN model2_id 
                                ELSE model1_id 
                            END as related_id', [$this->id])
                        ->pluck('related_id');
        
        return $relatedIds;
    }


    public function spaces()
    {
        return $this->belongsToMany(Space::class, 'relations', 'model2_id', 'model1_id')
                    ->where('relations.model1_type', 'SPACE')
                    ->where('relations.model2_type', 'PLAY')
                    ->withPivot('name', 'type', 'status', 'notes');
    }

    public function spacesWithDescendants()
    {
        $directSpaces = $this->spaces();

        $allSpaces = collect();

        foreach ($directSpaces->get() as $space) {
            $allSpaces = $allSpaces->merge($this->getAllDescendants($space));
        }

        return $allSpaces->unique('id')->values();
    }

    public function space_children($id)
    {
        $directSpaces = $this->spaces();

        return $directSpaces->where('parent_id', $id)->get();
    }

    protected function getAllDescendants($space)
    {
        $descendants = collect([$space]);

        foreach ($space->children as $child) {
            $descendants = $descendants->merge($this->getAllDescendants($child));
        }

        return $descendants;
    }
}