<?php

namespace App\Traits;

use App\Mail\WelcomeEmail;
use App\Models\Category;
use App\Models\Location;
use App\Models\User;
use App\Services\CategoryService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
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
    protected function registerUser(string $name, string $email, ?string $password = null, string $selectedPlan = 'free', $priceId = ''): User
    {
        $this->validateEmail($email);

        DB::beginTransaction();
        // If email validation succeeds, create the user, create password if Google Oauth
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password ? Hash::make($password) : Hash::make(Str::random(16)),
            'selected_plan' => $selectedPlan,
            'price_id' => $priceId,
        ]);

        // Fire registration event
        event(new Registered($user));

        // Log the user in
        Auth::login($user);

        // Create default location and root category
        Location::createLocation('Default Location', 'Feel free to change the description');
        $this->categoryService->createNewRootCategory();
        DB::commit();

        return $user;
    }

    /**
     * Validate email by attempting to send the welcome email.
     */
    protected function validateEmail(string $email): void
    {
        try {
            // First, validate the email with the DNS rule
            Validator::make(['email' => $email], [
                'email' => 'required|email|dns',
            ])->validate();  // Throws ValidationException if it fails

            // Then, attempt to send the welcome email
            Mail::to($email)->send(new WelcomeEmail(new User(['email' => $email])));

        } catch (ValidationException|TransportExceptionInterface $e) {
            throw ValidationException::withMessages(['email' => 'Invalid e-mail']);
        }
    }
}
