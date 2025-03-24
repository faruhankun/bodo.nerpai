<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    protected $table = 'company_settings';

    protected $fillable = [
        'module',
        'key', 
        'value', 
        'type', 
        'source_type', 
        'source_id',
    ];

    // Ambil setting tertentu dengan cache
    public static function get($key, $sourceType = null, $sourceId = null)
    {
        return cache()->remember("setting:$key:$sourceType:$sourceId", 3600, function () use ($key, $sourceType, $sourceId) {
            return self::where('key', $key)
                ->where('source_type', $sourceType)
                ->where('source_id', $sourceId)
                ->value('value') 
                ?? config("settings.company.$key");
        });
    }

    // Simpan atau update setting
    public static function set($key, $value, $module = null, $sourceType = null, $sourceId = null)
    {
        $setting = self::updateOrCreate(
            ['key' => $key, 'source_type' => $sourceType, 'source_id' => $sourceId],
            ['value' => $value, 'module' => $module]
        );

        // Reset cache
        cache()->forget("setting:$key:$sourceType:$sourceId");
        return $setting;
    }



    // relations
    public function source()
    {
        return $this->morphTo();
    }
}

