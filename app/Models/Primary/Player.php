<?php

namespace App\Models\Primary;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\User;

use Spatie\Permission\Traits\HasRoles;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;



class Player extends Model
{
    use HasRoles, SoftDeletes;

    protected $guarded = [];

    protected $guard_name = 'space';

    protected $connection = 'primary';
    
    protected $table = 'players';

    public $timestamps = true;

    protected $fillable = [
        'code',
        'type_type',
        'type_id',
        'size_type',
        'size_id',
        'space_type',
        'space_id',
        'name',
        'email',
        'phone_number',
        
        'address',
        'country',
        'province',
        'regency',
        'district',
        'sub_district',
        'village',
        'postal_code',
        'address_detail',

        'shopee_username',
        'tokopedia_username',
        'whatsapp_number',

        'status',
        'notes',

        'files',
        'tags',
        'links',
    ];

    protected $casts = [
        'address' => 'json',  
        'files' => 'json',
        'tags' => 'json',
        'links' => 'json',
    ];



    // address
    public function printAddress()
    {
        return ($this->province ? $this->province . ', ' : '')
             . ($this->regency ? ', ' .$this->regency : '')
             . ($this->district ? ', ' .$this->district : '')  
             . ($this->sub_district ? ', ' .$this->sub_district : '')
             . ($this->village ? ', ' .$this->village : '')
             . ($this->address_detail ? ', ' .$this->address_detail : '')
             . ($this->postal_code ? ', ' .$this->postal_code : '');
    }



    // marketplace
    public function printMarketplaceUsername()
    {
        return (
                ($this->shopee_username ? 'Shopee: ' .$this->shopee_username . '<br>' : '')
             . ($this->tokopedia_username ? 'Tokopedia: ' .$this->tokopedia_username . '<br>' : '')
             . ($this->whatsapp_number ? 'No Whatsapp: ' .$this->whatsapp_number : '')
                );
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


    public function transactions_as_receiver($limit = 10)
    {
        $query = $this->hasMany(Transaction::class, 'receiver_id', 'id')
                    ->where('receiver_type', 'PLAY');

        if($limit){
            $query->limit($limit);
        }
        
        $query = $query->orderBy('sent_time', 'desc');

        return $query;
    }


    public function space_relations()
    {
        return $this->hasMany(Relation::class, 'model2_id', 'id')
                    ->where('model1_type', 'SPACE')
                    ->where('model2_type', 'PLAY');
    }


    public function space()
    {
        return $this->morphTo();
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