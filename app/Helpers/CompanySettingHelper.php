<?php

use App\Models\Company\CompanySetting;

if (!function_exists('get_company_setting')) {
    function get_company_setting($key, $sourceType = null, $sourceId = null) {
        return CompanySetting::get($key, $sourceType, $sourceId);
    }
}