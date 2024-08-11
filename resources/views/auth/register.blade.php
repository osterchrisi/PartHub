@extends('centered-layout')
@section('content')
    <div class="row">
        <div class="col text-center">
            <h1 class="display-1" id="welcome-headline">PartHub</h1><br>
            <h1>Pricing</h1><br>

            <p style="line-height: 1.5;">Focus on the fun part of making things<br>
                Let PartHub take care of the annoying tasks of stock keeping for you!<br><br>
                Currently PartHub is in beta
                and it's a really good time to snatch a free
                early-bird account!</p>
            <p class="lead fw-bold"> There will always be a free tier for the enthusiasts!</p>
        </div>
        <div class="col bg-light pt-2 rounded">

            <h4 class="alert alert-info mt-1 text-center" role="alert">Now accepting sign ups for beta usage</h4>
            <div class="text-center mb-3">
                <h2>Create your free account</h2>
            </div>

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
        </div>
    </div>
@endsection

<style>
    .g-recaptcha {
        display: inline-block;
    }
</style>

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
