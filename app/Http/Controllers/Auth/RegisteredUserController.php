<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeEmail;
use App\Models\User;
use App\Models\Location;
use App\Models\Category;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register', ['title' => 'Signup', 'view' => 'signup']);
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate request
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'recaptcha_response' => ['required', 'string'],
        ]);

        // Recaptcha processing
        $this->validateRecaptcha($request);

        // Use the centralized registration logic
        $user = $this->registerUser($validated['name'], $validated['email'], $validated['password']);

        // Redirect after registration
        return redirect(RouteServiceProvider::HOME)->with('firstLogin', true);
    }

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
        event(new Registered($user));

        // Log the user in
        Auth::login($user);

        // Assign free subscription and create default location and category
        // $user->assignFreeSubscription(); //! Not doing it right now. Stripe fails because doesn't know address and maybe not such a great idea anyway...
        Location::createLocation("Default Location", "Feel free to change the description");
        Category::createNewRootCategory();

        return $user;
    }

    /**
     * Validate the reCAPTCHA response.
     */
    protected function validateRecaptcha(Request $request): void
    {
        $recaptchaResponse = $request->input('recaptcha_response');
        $siteVerify = Http::asForm()
            ->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('services.recaptcha.secretKey'),
                'response' => $recaptchaResponse,
            ]);
        $recaptcha = $siteVerify->json();

        if (!$recaptcha['success']) {
            throw ValidationException::withMessages(['recaptcha' => 'reCAPTCHA validation failed.']);
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
            // Handle email failure
            throw ValidationException::withMessages(['email' => 'Failed to send welcome email.']);
        }
    }

    /**
     * Validate the email by attempting to send the welcome email.
     */
    protected function validateEmailCanReceiveMail(string $email): void
    {
        try {
            Mail::to($email)->send(new WelcomeEmail(new User(['email' => $email])));
        } catch (TransportExceptionInterface $e) {
            throw ValidationException::withMessages(['email' => 'Invalid e-mail']);
        }
    }
}