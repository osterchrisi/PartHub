@extends('centered-layout')
@section('content')
    <div class="row">
        <div class="col">
            <table class="table table-borderless text-center mx-auto w-auto" style="borders: false">
                <tr>
                    <td colspan="4">
                        <h1 class="display-1" id="welcome-headline">PartHub</h1><br>
                        <h1>Pricing</h1><br>

                        <p style="line-height: 1.5;">PartHub helps you focus on the fun part of making things<br>
                            by taking care of the annoying tasks of stock keeping for you!<br><br>
                            Currently PartHub is in beta<br>
                            and it's a really good time to snatch afree
                            early-bird account!</p>
                        <p class="lead fw-bold"> There will always be a free tier for the enthusiasts!</p>
                    </td>
                </tr>
            </table>
        </div>
        <div class="col bg-light">
            <table class="table table-borderless text-center mx-auto w-auto" style="borders: false">
                <thead>
                    <tr>
                        <th>
                            <h4 class="alert alert-info" role="alert">Now accepting sign ups for beta usage</h4>
                            <h2>Create your free PartHub account</h2>
                            @php
                                if (isset($_GET['cnv'])) {
                                    echo '<div class="alert alert-dark" role="alert">reCAPTCHA was not verified</div>';
                                }
                            @endphp
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <tr>
                            <td style='text-align:left'>
                                {{-- User Name --}}
                                <label for="name" class="form-label">User Name</label>
                                <input class="form-control" id="name" type="text" name="name" required autofocus
                                    autocomplete="name">
                                <x-input-error :messages="$errors->get('name')"/>

                                {{-- Email Address --}}
                                <label for="email" class="form-label">Email</label>
                                <input id="email" type="email" class="form-control" type="email" name="email"
                                    required autocomplete="username">
                                <x-input-error :messages="$errors->get('email')"/>

                                {{-- Password --}}
                                <label for="password" class="form-label">Password</label>
                                <input id="password" type="password" name="password" required autocomplete="new-password"
                                    class="form-control">
                                <x-input-error :messages="$errors->get('password')"/>

                                {{-- Confirm Password --}}
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input id="password_confirmation" type="password" name="password_confirmation" required
                                    autocomplete="new-password" class="form-control">
                                <x-input-error :messages="$errors->get('password_confirmation')"/>


                            </td>
                        </tr>
                        <tr>
                            <td>
                                <button type="submit" class="btn btn-primary" id="signupBtn" disabled>Sign up</button>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align:center">
                                <p class="fw-light">We don't tend lightly to bots around here</p>
                                <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.siteKey') }}"
                                    data-callback="processChallenge">
                                    <x-input-error :messages="$errors->get('recaptcha')" class="mt-2" />
                                </div>
                                <input type="hidden" id="recaptchaResponse" name="recaptcha_response">
                            </td>
                        </tr>
                    </form>
                </tbody>
            </table>
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
