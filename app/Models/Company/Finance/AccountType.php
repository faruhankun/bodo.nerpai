<?php

namespace App\Models\Company\Finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountType extends Model
{
    use SoftDeletes;

    protected $table = 'account_types';

    public $timestamps = true;

    protected $fillable = [
        'basecode',
        'name',
        'type',
        'debit',
        'credit',
    ];
}
