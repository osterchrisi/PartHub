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
use Illuminate\Validation\Rules;
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
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate Request
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'recaptcha_response' => ['required', 'string'],
        ]);

        // Recaptcha processing
        $recaptcha_response = $request->input('recaptcha_response');

        $siteVerify = Http::asForm()
            ->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('services.recaptcha.secretKey'),
                'response' => $recaptcha_response,
            ]);


        $recaptcha = $siteVerify->json();

        if (!$recaptcha['success']) {
            return redirect()->route('register')->withErrors(['recaptcha' => 'reCAPTCHA validation failed.'])->withInput();
        }

        // Create User
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Onboarding message
        Session::put('firstLogin', true);

        // Create a default location, so user can start adding parts immediately
        Location::createLocation("Default Location", "Feel free to change the description");

        // Create a root category
        Category::createNewRootCategory();


        // Send welcome e-mail
        try {
            // Send welcome email
            Mail::to($request->email)->send(new WelcomeEmail($user));

            // Redirect to success page or show success message
            // return redirect()->route('success')->with('message', 'Registration successful! Check your email for further instructions.');
        } catch (TransportExceptionInterface $e) {
            //! Log the exception or handle it appropriately
            // Redirect back with an error message
            return back()->withErrors(['email' => 'Failed to send welcome email. Please check your email address.']);
        }

        return redirect(RouteServiceProvider::HOME);
    }
}
