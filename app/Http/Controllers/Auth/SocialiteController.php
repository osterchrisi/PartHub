<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Log;



class SocialiteController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Check if the user already exists
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Log the user in if they already exist
                Auth::login($user);
            }
            else {
                // Use the existing registration logic to register the user
                $registeredUserController = new RegisteredUserController();
                $registeredUserController->registerUser($googleUser->getName(), $googleUser->getEmail(), null);
            }

            // Redirect to home or intended route
            return redirect()->intended(RouteServiceProvider::HOME)->with('loggedIn', true);
        } catch (\Exception $e) {
            Log::error('Google login failed', ['exception' => $e]);
            return Redirect::route('login')->with('error', 'Login failed.');
        }
    }
}
