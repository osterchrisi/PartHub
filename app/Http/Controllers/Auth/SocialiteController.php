<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
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
    public function redirectToGoogle(Request $request)
    {
        \Log::info('Request data', $request->all());
        // Store selected plan and price in session before redirecting
        session([
            'selected_plan' => $request->input('plan', 'free'), // Default to 'free'
            'price_id' => $request->input('priceId', ''), // Default to empty string
        ]);
        \Log::info('Session data', session()->all());
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
            }
            else {
                // Retrieve plan & price from session (fallback to defaults)
                $selectedPlan = session('selected_plan', 'free');
                $priceId = session('price_id', '');
                // Register the user
                $this->registerUser($googleUser->getName(), $googleUser->getEmail(), null, $selectedPlan, $priceId);

                return redirect(RouteServiceProvider::HOME)->with('firstLogin', true);
            }

            // Redirect to home or intended route
            return redirect()->intended(RouteServiceProvider::HOME)->with('loggedIn', true);
        } catch (\Exception $e) {
            Log::error('Google login failed', ['exception' => $e]);

            return Redirect::route('login')->with('error', 'Login failed.');
        }
    }
}
