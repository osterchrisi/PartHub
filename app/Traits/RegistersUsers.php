<?php

namespace App\Traits;

use App\Models\User;
use App\Mail\WelcomeEmail;
use App\Models\Location;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

trait RegistersUsers
{
    /**
     * Centralized registration logic.
     */
    protected function registerUser(string $name, string $email, string $password = null): User
    {
        // Try to send the welcome email before creating the user
        $this->validateEmailCanReceiveMail($email);

        // If email validation succeeds, create the user
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password ? Hash::make($password) : Hash::make(Str::random(16)),
        ]);

        // Fire registration event
        event(new \Illuminate\Auth\Events\Registered($user));

        // Log the user in
        Auth::login($user);

        // Assign free subscription and create default location and category
        // $user->assignFreeSubscription(); //! Not doing it right now. Stripe fails because doesn't know address and maybe not such a great idea anyway...
        Location::createLocation("Default Location", "Feel free to change the description");
        Category::createNewRootCategory();

        return $user;
    }

    /**
     * Validate email by attempting to send the welcome email.
     */
    protected function validateEmailCanReceiveMail(string $email): void
    {
        try {
            Mail::to($email)->send(new WelcomeEmail(new User(['email' => $email])));
        } catch (TransportExceptionInterface $e) {
            throw ValidationException::withMessages(['email' => 'Invalid e-mail']);
        }
    }

    /**
     * Send the welcome email to the user.
     */
    protected function sendWelcomeEmail(User $user): void
    {
        try {
            Mail::to($user->email)->send(new WelcomeEmail($user));
        } catch (TransportExceptionInterface $e) {
            throw ValidationException::withMessages(['email' => 'Failed to send welcome email.']);
        }
    }
}
