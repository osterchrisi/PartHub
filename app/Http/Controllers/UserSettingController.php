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
        $hasMouserKey = !empty($this->userSettingService->getMouserApiKeyDecrypted($userId));

        return view('profile.user-settings', [
            'title' => 'User Settings',
            'view' => 'user-settings',
            'userTimezone' => $userTimezone,
            'hasMouserKey' => $hasMouserKey,
        ]);
    }

    public function update(Request $request)
    {
        $userId = Auth::id();

        // Allow partial updates + mouser fields
        $validated = $request->validate([
            'timezone' => 'sometimes|timezone',
            'mouser_enabled' => 'sometimes|boolean',
            'mouser_api_key' => 'nullable|string',
        ]);

        // Update timezone only if it was submitted
        if (array_key_exists('timezone', $validated)) {
            $this->userSettingService->updateOrCreateSetting($userId, 'timezone', $validated['timezone']);
        }

        // Update mouser toggle only if submitted
        if (array_key_exists('mouser_enabled', $validated)) {
            $enabled = (int) $validated['mouser_enabled'] === 1;
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

        // Store Mouser API key only if user entered one (do not overwrite with empty)
        if (!empty($validated['mouser_api_key'] ?? null)) {
            $this->userSettingService->setMouserApiKey($userId, $validated['mouser_api_key']);
        }

        return redirect()->back()->with('success', 'Settings updated successfully!');
    }


    // Get a user setting dynamically by setting name
    public function getUserSetting(Request $request, $setting_name)
    {
        $userId = auth()->id();

        //Protect Mouser API key
        if ($setting_name === 'mouser_api_key') {
            return response()->json(['message' => 'Not allowed'], 403);
        }

        // Retrieve setting
        $settingValue = $this->userSettingService->getSetting($userId, $setting_name);

        return response()->json([$setting_name => $settingValue]);
    }

    // Update or create a user setting
    public function updateUserSetting(Request $request, $setting_name)
    {
        $userId = auth()->id();

        
        $allowed = [
            'stocklevel_notification',
            'mouser_enabled',
            'mouser_api_key',
            // later: 'timezone' if ever migrated to this endpoint
        ];

        if (!in_array($setting_name, $allowed, true)) {
            return response()->json(['message' => 'Unknown setting'], 404);
        }

        // Special case: write-only Mouser API Key
        if ($setting_name === 'mouser_api_key') {
            $value = trim((string) $request->input('value', ''));

            // validate: non-empty, reasonable length
            if ($value === '') {
                return response()->json(['message' => 'API key cannot be empty'], 422);
            }
            if (mb_strlen($value) > 255) {
                return response()->json(['message' => 'API key is too long'], 422);
            }

            $this->userSettingService->setMouserApiKey($userId, $value);
            return response()->json(['message' => 'Mouser API key saved']);
        }

        // Boolean settings validation
        $value = (string) $request->input('value', '');

        if (!in_array($value, ['true', 'false'], true)) {
            return response()->json(['message' => 'Invalid value, expected true/false'], 422);
        }

        $this->userSettingService->updateOrCreateSetting($userId, $setting_name, $value);

        return response()->json(['message' => 'Setting updated successfully']);
    }

}
