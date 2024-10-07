<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        // If the user has already verified their email, redirect them to the home page
        if ($request->user()->hasVerifiedEmail()) {
            return $this->redirectAfterVerification($request);
        }

        // Mark the email as verified
        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        // Redirect after verification based on the plan
        return $this->redirectAfterVerification($request);
    }

    /**
     * Handle the redirection after email verification based on the selected plan.
     */
    protected function redirectAfterVerification(EmailVerificationRequest $request): RedirectResponse
    {
        // Retrieve the plan from the query string, default to 'free' if not provided
        $selectedPlan = $request->query('plan', 'free');
        $priceId = $request->query('priceId', '');

        // Redirect based on the selected plan
        if ($selectedPlan === 'maker') {
            // Redirect to the subscription checkout page for paid plans
            return redirect()->route('subscription.checkout', ['plan' => $selectedPlan, 'priceId' => $priceId]);
        }

        // Redirect to home or dashboard for free plan users
        return redirect()->intended(RouteServiceProvider::HOME.'?verified=1');
    }
}
