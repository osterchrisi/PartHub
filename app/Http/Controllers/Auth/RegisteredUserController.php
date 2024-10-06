<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Traits\RegistersUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

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
        \Log::info($request);
        // Validate request
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'recaptcha_response' => ['required', 'string'],
        ]);

        // Recaptcha processing
        $this->validateRecaptcha($request);

        // Selected plan
        $selectedPlan = $request->input('plan', 'free');
        $priceId = $request->input('priceId', '');

        // Use the centralized registration logic
        $user = $this->registerUser($validated['name'], $validated['email'], $validated['password'], $selectedPlan, $priceId);

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

        if (! $recaptcha['success']) {
            throw ValidationException::withMessages(['recaptcha' => 'reCAPTCHA validation failed.']);
        }
    }
}
