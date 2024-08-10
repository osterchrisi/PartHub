<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;



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

            // Check if the user already exists in your database
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // If user exists, log them in
                Auth::login($user);
            }
            else {
                // If user does not exist, create a new user
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => Hash::make(Str::random(16)), // Generate random password
                ]);

                Auth::login($user);
            }

            // Redirect to the checkout screen
            return Redirect::route('checkout');
        } catch (\Exception $e) {
            // Handle errors here
            return Redirect::route('login')->withErrors('Login failed.');
        }
    }
}