<?php

namespace App\Http\Controllers;

use App\Services\UserSettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserSettingController extends Controller
{
    protected $userSettingService;

    public function __construct(UserSettingService $userSettingService)
    {
        $this->userSettingService = $userSettingService;
    }

    public function index(Request $request)
    {
        $userId = Auth::id();

        // Available User Settings
        $userTimezone = $this->userSettingService->getUserTimezone($userId);
        $mouserEnabled = $this->userSettingService->isMouserEnabled($userId);
        $hasMouserKey = !empty($this->userSettingService->getMouserApiKeyDecrypted($userId));

        return view('profile.user-settings', [
            'title' => 'User Settings',
            'view' => 'user-settings',
            'userTimezone' => $userTimezone,
            'mouserEnabled' => $mouserEnabled,
            'hasMouserKey' => $hasMouserKey,
        ]);
    }

    public function update(Request $request)
    {
        $userId = Auth::id();

        \Log::info('UserSettingController@update HIT', [
            'userId' => $userId,
            'all' => $request->all(),
        ]);


        // Allow partial updates + mouser fields
        $validated = $request->validate([
            'timezone' => 'sometimes|timezone',
            'mouser_enabled' => 'sometimes|boolean',
            'mouser_api_key' => 'nullable|string',
        ]);

        \Log::info('Updating user settings for user ID: ' . $userId, ['validated' => $validated]);

        // UPDATE timezone only if it was submitted
        if (array_key_exists('timezone', $validated)) {
            $this->userSettingService->updateOrCreateSetting($userId, 'timezone', $validated['timezone']);
        }

        // UPDATE mouser toggle only if submitted
        if (array_key_exists('mouser_enabled', $validated)) {
            $enabled = (bool) $validated['mouser_enabled'];
            $this->userSettingService->setMouserEnabled($userId, $enabled);

            // If enabling: require a key if none exists yet AND none submitted now
            if ($enabled) {
                $hasKeyAlready = !empty($this->userSettingService->getMouserApiKeyDecrypted($userId));
                $submittedKey = trim((string) ($validated['mouser_api_key'] ?? ''));

                if (!$hasKeyAlready && $submittedKey === '') {
                    return redirect()->back()
                        ->withErrors(['mouser_api_key' => 'Please enter your Mouser API key to enable Mouser Search.'])
                        ->withInput();
                }
            }
        }

        // STORE key only if user entered one (do not overwrite with empty)
        if (!empty($validated['mouser_api_key'] ?? null)) {
            $this->userSettingService->setMouserApiKey($userId, $validated['mouser_api_key']);
        }

        return redirect()->back()->with('success', 'Settings updated successfully!');
    }


    // Get a user setting dynamically by setting name
    public function getUserSetting(Request $request, $setting_name)
    {
        $userId = auth()->id();

        // Retrieve setting
        $settingValue = $this->userSettingService->getSetting($userId, $setting_name, 'true'); // Default is 'true'

        return response()->json([$setting_name => $settingValue]);
    }

    // Update or create a user setting
    public function updateUserSetting(Request $request, $setting_name)
    {
        $userId = auth()->id();

        // Get the value from the request body, with an optional default fallback
        $value = $request->input('value', 'true'); // Default is 'true'

        // Update or create the setting
        $this->userSettingService->updateOrCreateSetting($userId, $setting_name, $value);

        return response()->json(['message' => 'Setting updated successfully']);
    }
}
