<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Mail\EmailChangeVerification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {

        if (config('app.env') == 'demo') {
            return Redirect::route('dashboard')->with('status', 'profile-demo-change');
        }

        $user = $request->user();
        $user->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            Log::info('Email is dirty');
            // Generate a verification token
            $verificationToken = Str::random(32);

            // Construct the verification URL
            $verificationUrl = route('email.verify', ['token' => $verificationToken]);

            // Store the pending email and token in the `email_changes` table
            DB::table('email_changes')->insert([
                'user_id' => $user->id,
                'new_email' => $request->input('email'),
                'verification_token' => $verificationToken,
                'expires_at' => now()->addHours(24),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            try {
                // Send the verification email to the new email address
                Mail::to($request->input('email'))->send(new EmailChangeVerification($verificationUrl, $user->name));

                return Redirect::route('dashboard')->with('status', 'email-changed');
            } catch (\Exception $e) {
                // Log the error for debugging purposes
                Log::error('Email could not be sent: '.$e->getMessage());

                // Optionally, remove the pending email change record to prevent issues
                DB::table('email_changes')
                    ->where('user_id', $user->id)
                    ->where('new_email', $request->input('email'))
                    ->delete();

                // Inform the user that the email verification failed
                return Redirect::route('dashboard')->withErrors([
                    'email' => 'The email verification could not be sent. Please check the email address and try again.',
                ]);
            }
        }

        // If email isn't being changed, just save the other profile data
        $user->save();

        return Redirect::route('dashboard')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();
        //! Delete user data here too! Part, BOMs, ...

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
