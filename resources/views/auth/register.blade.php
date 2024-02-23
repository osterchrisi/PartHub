@extends('centered-layout')
@section('content')
    <table class="table table-borderless text-center mx-auto w-auto" style="borders: false">
        <thead>
            <tr>
                <th>
                    <h4 class="alert alert-info" role="alert">Now accepting sign ups for beta usage</h4>
                    <h2>Sign up for a free PartHub account</h2>
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
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />

                        {{-- Email Address --}}
                        <label for="email" class="form-label">Email</label>
                        <input id="email" type="email" class="form-control" type="email" name="email" required
                            autocomplete="username">
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />

                        {{-- Password --}}
                        <label for="password" class="form-label">Password</label>
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                            class="form-control">
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />

                        {{-- Confirm Password --}}
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                            autocomplete="new-password" class="form-control">
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />


                    </td>
                </tr>
                <tr>
                    <td>
                        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                        <button type="submit" class="btn btn-primary" id="signupBtn" disabled>Sign up</button>
                    </td>
                </tr>
                <tr>
                    <td style="text-align:center">
                        <p class="fw-light">We don't tend lightly to bots around here</p>
                        <div class="g-recaptcha" data-sitekey="6Lca_UAlAAAAAHLO5OHYaIvpzTPZAhf51cvz3LZE"
                            data-callback="enableSignupBtn">
                        </div>
                        <input type="hidden" id="recaptchaResponse" name="recaptcha_response">
                    </td>
                </tr>
            </form>
        </tbody>
    </table>
    {{-- <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required
                autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required
                autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation"
                required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ml-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form> --}}
@endsection

<style>
    .g-recaptcha {
        display: inline-block;
    }
</style>

<script>
    //TODO: Would maybe be nice to add a listener to the button, telling the user to complete the challenge first
    function enableSignupBtn() {
        document.getElementById('signupBtn').disabled = false;
    }

    grecaptcha.ready(function() {
        grecaptcha.execute('6Lca_UAlAAAAAHLO5OHYaIvpzTPZAhf51cvz3LZE', {
                action: 'signup'
            })
            .then(function(token) {
                document.getElementById('recaptchaResponse').value = token;
            });
    });
</script>