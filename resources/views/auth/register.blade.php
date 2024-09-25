@extends('centered-layout')




@section('content')
    <div class="container my-5">

        <div class="row hero-section rounded-3 mb-5 justify-content-center text-center"
            style="background-image: url('app-related/hero-background.webp'); background-size: cover; background-position: center;">
            <div class="col-md-8 text-white">
                <h1 class="display-1 mb-4">Get Started with PartHub</h1>
                <p class="lead mb-4 bg-dark text-white p-2 rounded d-inline-block p-hero">Focus on the fun part of making
                    things!</p><br>
                <a href="#pricing" class="btn btn-lg btn-light me-2">See Pricing</a>
            </div>
        </div>

        <x-register-card bgClass="text-white signup-gradient-background">
            <h4 class="alert alert-info mt-1 text-center" role="alert">Now accepting sign ups for beta usage</h4>
            <div class="text-center mb-4">
                <h2 class="display-5">Create Your Free Account</h2>
            </div>
            <!-- Social Sign Up -->
            <div class="text-center mb-3">
                <a href="{{ route('google.login') }}" class="btn btn-lg btn-outline-light">
                    <i class="fab fa-google me-2"></i>Sign up with Google
                </a>
            </div>

            <!-- Divider -->
            <div class="d-flex justify-content-center align-items-center my-4">
                <hr class="flex-grow-1">
                <span class="mx-3 text-muted">OR</span>
                <hr class="flex-grow-1">
            </div>
            <div class="text-start">
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    {{-- User Name --}}
                    <label for="name" class="form-label">User Name</label>
                    <input class="form-control" id="name" type="text" name="name" required autofocus
                        autocomplete="name">
                    <x-input-error :messages="$errors->get('name')" />

                    {{-- Email Address --}}
                    <label for="email" class="form-label">Email</label>
                    <input id="email" type="email" class="form-control" type="email" name="email" required
                        autocomplete="username">
                    <x-input-error :messages="$errors->get('email')" />

                    {{-- Password --}}
                    <label for="password" class="form-label">Password</label>
                    <input id="password" type="password" name="password" required autocomplete="new-password"
                        class="form-control">
                    <x-input-error :messages="$errors->get('password')" />

                    {{-- Confirm Password --}}
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                        autocomplete="new-password" class="form-control">
                    <x-input-error :messages="$errors->get('password_confirmation')" />

                    <br>
                    <div class="form-check form-switch mb-3">
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
        </x-register-card>

        <!-- Pricing Section -->
        <x-register-card bgClass="shadow-sm pricing-gradient-background text-white">
            <div class="text-center mb-4">
                <h2 class="display-4 mb-4">Pricing Plans</h2>
                <p class="lead">Pick the plan that best suits your needs.</p>
            </div>

            <!-- Pricing Table -->
            <div class="row justify-content-center text-black">
                <div class="col-md-4 mb-4">
                    <div class="card text-center border-0 shadow-sm">
                        <div class="card-body">
                            <h3 class="card-title">Free</h3>
                            <p class="lead text-muted">For small projects</p>
                            <h4 class="pricing">$0/month</h4>
                            <ul class="list-group my-4">
                                <li class="list-group-item">✔️ 100 parts limit</li>
                                <li class="list-group-item">✔️ 1 storage location</li>
                                <li class="list-group-item">✔️ Community support</li>
                            </ul>
                            <a href="" class="btn btn-outline-primary btn-lg">Sign Up for Free</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="card text-center border-0 shadow-sm">
                        <div class="card-body">
                            <h3 class="card-title">Pro</h3>
                            <p class="lead text-muted">For growing businesses</p>
                            <h4 class="pricing">$29/month</h4>
                            <ul class="list-group my-4">
                                <li class="list-group-item">✔️ Unlimited parts</li>
                                <li class="list-group-item">✔️ Multiple storage locations</li>
                                <li class="list-group-item">✔️ Supplier management</li>
                                <li class="list-group-item">✔️ Premium support</li>
                            </ul>
                            <a href="" class="btn btn-primary btn-lg">Start Pro Trial</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="card text-center border-0 shadow-sm">
                        <div class="card-body">
                            <h3 class="card-title">Enterprise</h3>
                            <p class="lead text-muted">For large-scale businesses</p>
                            <h4 class="pricing">Contact Us</h4>
                            <ul class="list-group my-4">
                                <li class="list-group-item">✔️ Customized solutions</li>
                                <li class="list-group-item">✔️ Advanced BOM management</li>
                                <li class="list-group-item">✔️ Dedicated support team</li>
                            </ul>
                            <a href="" class="btn btn-outline-primary btn-lg">Contact Us</a>
                        </div>
                    </div>
                </div>
            </div>
        </x-register-card>




        {{-- <!-- Sign Up Section -->
        <x-register-card bgClass="card-light-bg shadow-sm">
            <h4 class="alert alert-info mt-1 text-center" role="alert">Now accepting sign ups for beta usage</h4>
            <div class="text-center mb-4">
                <h2 class="display-5">Create Your Free Account</h2>
            </div>
            <!-- Social Sign Up -->
            <div class="text-center mb-3">
                <a href="{{ route('google.login') }}" class="btn btn-lg btn-outline-primary">
                    <i class="fab fa-google me-2"></i>Sign up with Google
                </a>
            </div>

            <!-- Divider -->
            <div class="d-flex justify-content-center align-items-center my-4">
                <hr class="flex-grow-1">
                <span class="mx-3 text-muted">OR</span>
                <hr class="flex-grow-1">
            </div>

            <!-- Standard Sign Up Form -->
            <div class="text-start">
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- User Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label">User Name</label>
                        <input class="form-control" id="name" type="text" name="name" required autofocus
                            autocomplete="name">
                        <x-input-error :messages="$errors->get('name')" />
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" type="email" class="form-control" name="email" required
                            autocomplete="username">
                        <x-input-error :messages="$errors->get('email')" />
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                            class="form-control">
                        <x-input-error :messages="$errors->get('password')" />
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                            autocomplete="new-password" class="form-control">
                        <x-input-error :messages="$errors->get('password_confirmation')" />
                    </div>

                    <!-- Terms Agreement -->
                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" required>
                        <label class="form-check-label" for="flexSwitchCheckDefault">I agree to the <a
                                href="{{ route('TOS') }}" target="_blank">Terms of Service</a> and <a
                                href="{{ route('privacy-policy') }}" target="_blank">Privacy Policy</a></label>
                    </div>

                    <!-- Recaptcha -->
                    <div class="text-center mt-3">
                        <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.siteKey') }}"
                            data-callback="processChallenge">
                            <x-input-error :messages="$errors->get('recaptcha')" class="mt-2" />
                        </div>
                        <input type="hidden" id="recaptchaResponse" name="recaptcha_response">
                    </div>

                    <!-- Submit Button -->
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-lg btn-primary" id="signupBtn" disabled>Create
                            account</button>
                    </div>
                </form>
            </div>
        </x-register-card>

        <!-- Pricing Section -->
        <x-register-card bgClass="bg-gradient card-primary-bg text-white shadow-sm">
            <div class="text-center mb-4">
                <h1 class="display-1">PartHub Pricing</h1>
                <h3 class="display-5 my-4">Focus on the fun part of making things</h3>
            </div>

            <!-- Pricing Table -->
            <div class="row justify-content-center">
                <div class="col-md-4 mb-4">
                    <div class="card text-center border-0 shadow-sm">
                        <div class="card-body">
                            <h3 class="card-title">Free</h3>
                            <p class="lead text-muted">For small projects</p>
                            <h4 class="pricing">$0/month</h4>
                            <ul class="list-unstyled my-4">
                                <li>✔️ 100 parts limit</li>
                                <li>✔️ 1 storage location</li>
                                <li>✔️ Community support</li>
                            </ul>
                            <a href="" class="btn btn-outline-primary btn-lg">Sign Up for Free</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="card text-center border-0 shadow-sm">
                        <div class="card-body">
                            <h3 class="card-title">Pro</h3>
                            <p class="lead text-muted">For growing businesses</p>
                            <h4 class="pricing">$29/month</h4>
                            <ul class="list-unstyled my-4">
                                <li>✔️ Unlimited parts</li>
                                <li>✔️ Multiple storage locations</li>
                                <li>✔️ Supplier management</li>
                                <li>✔️ Premium support</li>
                            </ul>
                            <a href="}" class="btn btn-primary btn-lg">Start Pro Trial</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="card text-center border-0 shadow-sm">
                        <div class="card-body">
                            <h3 class="card-title">Enterprise</h3>
                            <p class="lead text-muted">For large-scale businesses</p>
                            <h4 class="pricing">Contact Us</h4>
                            <ul class="list-unstyled my-4">
                                <li>✔️ Customized solutions</li>
                                <li>✔️ Advanced BOM management</li>
                                <li>✔️ Dedicated support team</li>
                            </ul>
                            <a href="" class="btn btn-outline-primary btn-lg">Contact Us</a>
                        </div>
                    </div>
                </div>
            </div>
        </x-register-card> --}}
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
