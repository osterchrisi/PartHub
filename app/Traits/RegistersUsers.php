<?php

namespace App\Traits;

use App\Mail\WelcomeEmail;
use App\Models\Category;
use App\Models\Location;
use App\Models\User;
use App\Services\CategoryService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

trait RegistersUsers
{
    protected $categoryService;
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Register a new user, create related resources, and log them in.
     *
     * This method handles the entire user registration process, including
     * email validation, user creation, firing the registration event, logging
     * the user in, and creating default locations and categories.
     *
     * @param  string  $name  The name of the user.
     * @param  string  $email  The email address of the user.
     * @param  string|null  $password  The password of the user, or null to generate one.
     * @return \App\Models\User The newly registered user.
     *
     * @throws \Illuminate\Validation\ValidationException If email validation fails.
     */
    protected function registerUser(string $name, string $email, ?string $password = null): User
    {
        // Try to send the welcome email before creating the user
        //TODO: Use something better than sending a mail to verify... Laravel supports MX record validation, e.g. $request->validate(['email' => 'required|email|dns']);
        $this->validateEmailCanReceiveMail($email);

        // If email validation succeeds, create the user,  create password if google Oauth
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password ? Hash::make($password) : Hash::make(Str::random(16)),
        ]);

        // Fire registration event
        event(new Registered($user));

        // Log the user in
        Auth::login($user);

        // Create default location and root category
        Location::createLocation('Default Location', 'Feel free to change the description');
        $root_category = $this->categoryService->createNewRootCategory();

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
