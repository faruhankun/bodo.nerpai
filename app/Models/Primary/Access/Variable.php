<?php

namespace App\Models\Primary\Access;

use Illuminate\Database\Eloquent\Model;

class Variable extends Model
{
    protected $table = 'variables';

    protected $fillable = [
        'key',
        'space_type',
        'space_id',
        'parent_type',
        'parent_id',
        'model_type',
        'model_id',
        'type_type',
        'type_id',
        'expiry_time',
        'name',
        'value',
        'module',
        'status',
        'notes',
        'deletable',
    ];



    // Ambil variable tertentu dengan cache
    public static function get($key, $spaceType = null, $spaceId = null)
    {
        return cache()->remember("variable:$key:$spaceType:$spaceId", 3600, function () use ($key, $spaceType, $spaceId) {
            return self::where('key', $key)
                ->where('space_type', $spaceType)
                ->where('space_id', $spaceId)
                ->value('value')
                ?? config("variables.space.$key");
        });
    }

    // Simpan atau update variable
    public static function set($key, $value, $module = null, $spaceType = null, $spaceId = null)
    {
        $variable = self::updateOrCreate(
            ['key' => $key, 'space_type' => $spaceType, 'space_id' => $spaceId],
            ['value' => $value, 'module' => $module]
        );

        // Reset cache
        cache()->forget("variable:$key:$spaceType:$spaceId");
        return $variable;
    }



    // relations
    public function space()
    {
        return $this->morphTo();
    }

    public function parent()
    {
        return $this->morphTo();
    }

    public function type()
    {
        return $this->morphTo();
    }
}

