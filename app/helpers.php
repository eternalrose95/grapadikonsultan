<?php

use App\Models\SiteSetting;

if (!function_exists('settings')) {
    /**
     * Get a site setting value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function settings(string $key, mixed $default = null): mixed
    {
        return SiteSetting::get($key, $default);
    }
}

if (!function_exists('settings_group')) {
    /**
     * Get all settings for a group
     *
     * @param string $group
     * @return array
     */
    function settings_group(string $group): array
    {
        return SiteSetting::getGroup($group);
    }
}

if (!function_exists('site_setting')) {
    /**
     * Get a site setting value (alias for settings)
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function site_setting(string $key, mixed $default = null): mixed
    {
        return SiteSetting::get($key, $default);
    }
}
