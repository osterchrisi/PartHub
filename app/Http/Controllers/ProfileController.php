<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\EmailChangeVerification;


class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user()
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        
        $user = $request->user();
        $user->fill($request->validated());
        Log::info('Update request', [$request]);
        
    
        if ($request->user()->isDirty('email')) {
            Log::info('Email is diry');
            // Generate a verification token
            $verificationToken = Str::random(32);
      
            // Option 2: Store the pending email and token in the `email_changes` table
            DB::table('email_changes')->insert([
                'user_id' => $user->id,
                'new_email' => $request->input('email'),
                'verification_token' => $verificationToken,
                'expires_at' => now()->addHours(24),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
    
            // Send the verification email to the new email address
            Mail::to($request->input('email'))->send(new EmailChangeVerification($user, $verificationToken));
    
            return Redirect::route('dashboard')->with('status', 'email-verification-sent');
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
        //! Delete user data here too?

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
