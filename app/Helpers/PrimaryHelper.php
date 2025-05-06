<?php

use App\Models\Primary\Access\Variable;

if (!function_exists('get_variable')) {
    function get_variable($key, $sourceType = null, $sourceId = null) {
        if (is_null($sourceType) && is_null($sourceId)) {
            $sourceType = 'SPACE';
            $sourceId = session('space_id') ?? null;
        }
        
        return Variable::get($key, $sourceType, $sourceId);
    }
}