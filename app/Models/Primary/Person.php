<?php

namespace App\Models\Primary;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

use App\Models\Primary\Player;

class Person extends Model
{
    protected $table = 'persons';

    protected $connection = 'primary';

    protected $fillable = [
        'number',
        'name',
        'full_name',
        'birth_date',
        'death_date',
        'gender',
        'address',
        'email',
        'phone_number',    
        'status',
        'notes', 
    ];


    protected function casts(): array
    {
        return [  
            'address' => 'json',
        ];
    }


    public function generateNumber()
    {
        $this->number = $this->birth_date->format('Ymd') . '' . $this->id;
        return $this->number;
    }


    
    // relationship
    public function player(): hasOne
    {
        return $this->hasOne(Player::class, 'size_id', 'id')
                    ->where('size_type', 'PERS');
    }
}
