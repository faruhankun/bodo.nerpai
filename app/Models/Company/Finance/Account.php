<?php

namespace App\Models\Company\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use HasFactory, SoftDeletes;
    public $timestamps = true;
    protected $table = 'accounts';

    protected $fillable = [
        'code',
        'name',
        'type_id',
        'parent_id',
        'status',
        'balance',
        'notes',
    ];

    public function parent()
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    public function account_type()
    {
        return $this->belongsTo(AccountType::class, 'type_id');
    }
}
