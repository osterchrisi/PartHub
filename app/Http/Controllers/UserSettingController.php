<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserSettingService;
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
        $userTimezone = $this->userSettingService->getUserTimezone($userId);

        return view('profile.user-settings', [
            'title' => 'User Settings',
            'view' => 'user-settings',
            'userTimezone' => $userTimezone,
        ]);
    }

    public function update(Request $request)
    {
        $userId = Auth::id();

        // Validate the timezone input
        $validated = $request->validate([
            'timezone' => 'required|timezone', // Ensure the submitted timezone is valid
        ]);

        // Update or create the user's timezone setting
        $this->userSettingService->updateOrCreateSetting($userId, 'timezone', $validated['timezone']);

        // Redirect back with a success message
        //TODO: Not shown yet
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
