<?php

namespace App\Helpers;

use App\Models\WebsiteSetting;

class WebsiteHelper
{
    public static function getSetting($key = null, $default = null)
    {
        $settings = WebsiteSetting::first();
        
        if (!$settings) {
            return $default;
        }
        
        if ($key === null) {
            return $settings;
        }
        
        return $settings->$key ?? $default;
    }

    public static function getWebsiteName($default = 'Laravel Starter Kit')
    {
        return self::getSetting('website_name', $default);
    }

    public static function getLogo($default = null)
    {
        $logo = self::getSetting('logo');
        return $logo ? asset('storage/' . $logo) : $default;
    }

    public static function getFavicon($default = null)
    {
        $favicon = self::getSetting('favicon');
        return $favicon ? asset('storage/' . $favicon) : $default;
    }
}
