<?php

namespace App\Models\Space;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $table = 'persons';

    protected $connection = 'primary';

    protected $fillable = [
        'name',
        'number',
        'full_name',
        'birth_date',
        'death_date',
        'sex',
        'address',  
        'phone_number',    
        'status', 
    ];


    
    // relationship
    public function player(): hasOne
    {
        return $this->hasOne(Player::class, 'size_id', 'id')
                    ->where('size_type', 'PERS');
    }
}
