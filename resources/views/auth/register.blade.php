@extends('centered-layout')

@section('content')
    <div class="container-fluid my-5">

        <x-hero-card divExtraClass="justify-content-center text-center" backgroundImage="app-related/hero-background.webp"
            title="Sign Up to PartHub" titleExtraClass="" subtitle="Focus on the fun part of making
        things!"
            secondButtonRoute="#register-pricing" firstButtonRoute="#register-free" secondButtonText="See Pricing"
            firstButtonText="Sign Up For Free" />

        <x-register-card bgClass="text-white signup-gradient-background" id="register-free">
            <div class="text-center mb-4">
                <h2 class="display-5">Create Your Free Account</h2>
            </div>
            <!-- Social Sign Up -->
            <div class="text-center mb-3">
                <a href="{{ route('google.login') }}" class="btn btn-lg btn-outline-light" id="googleSignupBtn">
                    <i class="fab fa-google me-2"></i>Sign up with Google
                </a>
            </div>

            <!-- Divider -->
            <div class="d-flex justify-content-center align-items-center my-4">
                <hr class="flex-grow-1">
                <span class="mx-3 text-muted">OR</span>
                <hr class="flex-grow-1">
            </div>
            <form method="POST" action="{{ route('register') }}" id="register-form">
                @csrf
                <div class="text-start">
                    {{-- User Name --}}
                    <label for="name" class="form-label mt-2">User Name</label>
                    <input class="form-control" id="name" type="text" name="name" required autofocus
                        autocomplete="name">
                    <x-input-error :messages="$errors->get('name')" />

                    {{-- Email Address --}}
                    <label for="email" class="form-label mt-2">Email</label>
                    <input id="email" type="email" class="form-control" type="email" name="email" required
                        autocomplete="username">
                    <x-input-error :messages="$errors->get('email')" />

                    {{-- Password --}}
                    <label for="password" class="form-label mt-2">Password</label>
                    <input id="password" type="password" name="password" required autocomplete="new-password"
                        class="form-control" data-toggle="password">
                    <x-input-error :messages="$errors->get('password')" />

                    {{-- Confirm Password --}}
                    <label for="password_confirmation" class="form-label mt-2">Confirm Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                        autocomplete="new-password" class="form-control" data-toggle="password">
                    <x-input-error :messages="$errors->get('password_confirmation')" />

                    {{-- Hidden Input Fields for Subscription --}}
                    <input type="hidden" name="plan" value="{{ request('plan', 'free') }}">
                    <input type="hidden" name="priceId" value="{{ request('priceId', '') }}">

                    <div class="form-check form-switch mb-3 mt-3">
                        <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" required>
                        <label class="form-check-label" for="flexSwitchCheckDefault">I agree to the <a
                                href="{{ route('TOS') }}" target="_blank">Terms of Service</a> and <a
                                href="{{ route('privacy-policy') }}" target="_blank">Privacy Policy</a></label>
                    </div>
                </div>
                <div class="my-2 text-center">
                    <button type="submit" class="btn btn-lg btn-primary" id="signupBtn" disabled>Create account</button>
                </div>

                <div class="text-center mt-3">
                    <p class="fw-light">We don't tend lightly to bots around here</p>
                    <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.siteKey') }}"
                        data-callback="processChallenge">
                        <x-input-error :messages="$errors->get('recaptcha')" class="mt-2" />
                    </div>
                    <input type="hidden" id="recaptchaResponse" name="recaptcha_response">
                </div>
            </form>
        </x-register-card>

        <!-- Pricing Section -->
        <x-register-card bgClass="shadow-sm pricing-gradient-background text-white" id="register-pricing">
            <div class="text-center mb-4">
                <h2 class="display-4 mb-4">Looking for more?</h2>
                <p class="lead">Pick whatever floats your boat</p>
            </div>

            <!-- Pricing Table -->
            <div class="row justify-content-center text-black">

                {{-- Pro --}}
                <div class="col-auto mb-4">
                    <div class="card border-0 shadow-sm position-relative">
                        <!-- Ribbon Badge -->
                        <div class="ribbon text-white">Early Access Special</div>
                        <div class="card-body">
                            <h3 class="card-title">Maker 🚀</h3>
                            <p class="lead text-muted fs-6">Best for small-scale businesses</p>
                            <h4 class><del>€29/month</del></h4>
                            <h4 class="text-danger">€9/month</h4>
                            <ul class="list-group my-4 list-group-item-action text-start">
                                <li class="list-group-item list-group-item-action"><i
                                        class="fas fa-check pricing-tier-check"></i> Unlimited Resources</li>
                                <li class="list-group-item list-group-item-action"><i
                                        class="fas fa-check pricing-tier-check"></i> Multiple Storage Locations per Part
                                </li>
                                <li class="list-group-item list-group-item-action"><i
                                        class="fas fa-check pricing-tier-check"></i> Multiple Suppliers per Part</li>
                                <li class="list-group-item list-group-item-action"><i
                                        class="fas fa-check pricing-tier-check"></i> Stock Movement History</li>
                                <li class="list-group-item list-group-item-action"><i
                                        class="fas fa-check pricing-tier-check"></i> Minimum Stock Notifications</li>
                                <li class="list-group-item list-group-item-action"><i
                                        class="fas fa-check pricing-tier-check"></i> Images for Resources</li>
                                <li class="list-group-item list-group-item-action"><i
                                        class="fas fa-check pricing-tier-check"></i> Documents for Resources</li>
                                <li class="list-group-item list-group-item-action"><i
                                        class="fas fa-check pricing-tier-check"></i> Premium support</li>
                                <li class="list-group-item list-group-item-action"><i
                                        class="fas fa-check pricing-tier-check"></i> 14-day Free Trial Period</li>
                            </ul>
                            <div>
                                <a href="{{ route('register', ['plan' => 'maker', 'priceId' => 'price_1Q6cQPEb2UyIF2shSxSBcIox']) }}"
                                    class="disabled-link"><button class="btn btn-primary btn-lg" disabled>Start Pro
                                        Trial</button></a>
                            </div>
                            <div class="text-muted fw-light">Coming soon!</div>
                        </div>
                    </div>
                </div>

                {{-- Free --}}
                <div class="col-md-auto mb-4">
                    <div class="card text-center border-0 shadow-sm">
                        <div class="card-body">
                            <h3 class="card-title">Free</h3>
                            <p class="lead text-muted fs-6">Best for Enthusiasts and Tinkerers</p>
                            <h4 class>€0/month</h4>
                            <ul class="list-group my-4 text-start">
                                <li class="list-group-item"><i class="fas fa-check"></i> Limited Resources</li>
                                <li class="list-group-item list-group-item-action"><i class="fas fa-check"></i> Stock
                                    Movement History</li>
                                <li class="list-group-item"><i class="fas fa-check"></i> Community support</li>
                                <li class="list-group-item"><i class="fas fa-check"></i> Forever free 😇</li>
                            </ul>
                            <a href="#register-free" class="btn btn-outline-primary btn-lg">Sign Up for Free</a>
                        </div>
                    </div>
                </div>

            </div>
            <div class="text-center mb-4">
                <p class="lead">Resources are: Parts, BOMs, Locations, Categories, Suppliers</p>
            </div>
        </x-register-card>
    </div>
@endsection

<script async src="https://www.google.com/recaptcha/api.js"></script>

<script>
    if (typeof grecaptcha === 'undefined') {
        grecaptcha = {};
    }

    grecaptcha.ready = function(cb) {
        if (typeof grecaptcha === 'undefined') {
            const c = '___grecaptcha_cfg';
            window[c] = window[c] || {};
            (window[c]['fns'] = window[c]['fns'] || []).push(cb);
        } else {
            cb();
        }
    }

    function processChallenge() {
        document.getElementById('signupBtn').disabled = false; // Enable Signup Button after challenge is completed
        response = grecaptcha.getResponse(); // Get challenge response
        document.getElementById('recaptchaResponse').value = response; // Send challenge response with the form
    }
</script>
