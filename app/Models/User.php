<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;  // Pastikan Role diimport
use Spatie\Permission\Models\Permission;

use App\Models\Space\Company;

class User extends Authenticatable
{
    protected $table = 'users';

    protected $connection = 'primary';

    use HasFactory, Notifiable, HasRoles;

    protected $guard_name = 'web';

    // Kolom dan relasi yang ada di User tidak berubah
    protected $fillable = [
        'name',
        'email',
        'password',
        'tgl_lahir',  
        'alamat',    
        'no_hp',  
        'tgl_keluar',
        'role_id', 
        'player_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'tgl_lahir' => 'date',    
            'tgl_keluar' => 'date',   
        ];
    }

    // Relasi ke Company
    public function companies()
    {
        return $this->belongsToMany(Company::class, 'companies_users', 'user_id', 'company_id')
                    ->withPivot(('status'));
    }

    public function approvedCompanies(){
        return $this->companies()
                    ->wherePivot('status', 'approved');
    }


    public function companyusers()
    {
        return $this->hasMany(CompanyUser::class);
    }
    
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function player()
    {
        return $this->belongsTo(Player::class);
    }
}
