<?php

namespace App\Services;

use App\Models\UserSetting;

class UserSettingService
{
    // Class property holding default values for settings
    protected $defaultSettings = [
        'stocklevel_notification' => 'true',
        'timezone' => 'UTC',
    ];

    // Retrieve a specific setting for a user with a fallback default value
    //! Makes sense to return a default setting...?
    public function getSetting($userId, $settingName, $default = null)
    {

        $default = $this->defaultSettings[$settingName] ?? $default;

        $setting = UserSetting::where('user_id_fk', $userId)
            ->where('setting_name', $settingName)
            ->first();

        return $setting ? $setting->setting_value : $default;
    }

    // Retrieve the user's preferred timezone or default to 'UTC'
    public function getUserTimezone($userId)
    {
        // Use the 'getSetting' method to retrieve the timezone setting, default to 'UTC'
        return $this->getSetting($userId, 'timezone', 'UTC');
    }

    // Update or create a specific setting for a user
    public function updateOrCreateSetting($userId, $settingName, $value)
    {
        return UserSetting::updateOrCreate(
            ['user_id_fk' => $userId, 'setting_name' => $settingName],
            ['setting_value' => $value]
        );
    }

    // Check if stocklevel notification should be enabled for a user
    public function shouldNotify($userId)
    {
        return $this->getSetting($userId, 'stocklevel_notification', 'true') === 'true';
    }
}
