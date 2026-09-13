<?php

namespace App\Config;

use App\Models\SystemSettingModel;

class SidebarLogo
{
    /**
     * Setting key for the sidebar logo
     */
    public const SETTING_KEY = 'sidebar_logo';
    
    /**
     * Get the current sidebar logo filename
     *
     * @return string|null
     */
    public static function getLogo(): ?string
    {
        $settingModel = new SystemSettingModel();
        return $settingModel->getSetting(self::SETTING_KEY);
    }
    
    /**
     * Save the sidebar logo filename
     *
     * @param string $filename
     * @return bool
     */
    public static function saveLogo(string $filename): bool
    {
        $settingModel = new SystemSettingModel();
        return $settingModel->setSetting(self::SETTING_KEY, $filename, 'Logo sidebar untuk branding di sidebar');
    }
    
    /**
     * Remove the sidebar logo
     *
     * @return bool
     */
    public static function removeLogo(): bool
    {
        $settingModel = new SystemSettingModel();
        return $settingModel->deleteSetting(self::SETTING_KEY);
    }
}