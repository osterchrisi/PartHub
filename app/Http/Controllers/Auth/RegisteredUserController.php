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
use App\Traits\RegistersUsers;

class RegisteredUserController extends Controller
{
    use RegistersUsers;

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
}
