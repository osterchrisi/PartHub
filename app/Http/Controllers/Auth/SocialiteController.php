<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Traits\RegistersUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    use RegistersUsers;

    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the callback from Google after authentication.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Check if the user already exists
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Log the user in if they already exist
                Auth::login($user);
            } else {
                // Use the existing registration logic to register the user
                $this->registerUser($googleUser->getName(), $googleUser->getEmail(), null);
            }

            // Redirect to home or intended route
            return redirect()->intended(RouteServiceProvider::HOME)->with('loggedIn', true);
        } catch (\Exception $e) {
            Log::error('Google login failed', ['exception' => $e]);

            return Redirect::route('login')->with('error', 'Login failed.');
        }
    }
}
