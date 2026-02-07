<?php

namespace App\Services;

use App\Models\UserSetting;
use Illuminate\Support\Facades\Crypt;


class UserSettingService
{
    // Class property holding default values for settings
    protected $defaultSettings = [
        'stocklevel_notification' => 'true',
        'timezone' => 'UTC',
        'mouser_enabled' => 'false',
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

    public function isMouserEnabled(int $userId): bool
    {
        return $this->getSetting($userId, 'mouser_enabled', 'false') === 'true';
    }

    public function setMouserEnabled(int $userId, bool $enabled): void
    {
        $this->updateOrCreateSetting($userId, 'mouser_enabled', $enabled ? 'true' : 'false');
    }

    public function setMouserApiKey(int $userId, ?string $plainKey): void
    {
        // allow clearing
        if (!$plainKey) {
            $this->updateOrCreateSetting($userId, 'mouser_api_key', '');
            return;
        }

        $this->updateOrCreateSetting($userId, 'mouser_api_key', Crypt::encryptString($plainKey));
    }

    public function getMouserApiKeyDecrypted(int $userId): ?string
    {
        $enc = $this->getSetting($userId, 'mouser_api_key', null);
        if (!$enc)
            return null;

        try {
            return Crypt::decryptString($enc);
        } catch (\Throwable $e) {
            // corrupted/legacy values
            return null;
        }
    }
}
