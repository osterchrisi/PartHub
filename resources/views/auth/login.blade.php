@extends('centered-layout')

@section('content')
    <div class="container text-center mx-auto w-auto">
        <!-- Alert Messages -->
        @if (session('loggedOut'))
            <div class="alert alert-success">You've been successfully logged out 👍</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <!-- Header -->
        <h4 class="mb-4">Log in to your PartHub account</h4>

        <!-- Social Sign Up -->
        <div class="text-center mb-3">
            <a href="{{ route('google.login') }}" class="btn btn-lg btn-outline-primary">
                <i class="fab fa-google me-2"></i>Log In with Google
            </a>
        </div>

        <!-- Divider -->
        <div class="d-flex justify-content-center align-items-center my-4">
            <hr class="flex-grow-1">
            <span class="mx-3 text-muted">OR</span>
            <hr class="flex-grow-1">
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3 text-start">
                <label for="inputEmail" class="form-label">Email</label>
                <input id="inputEmail" type="email" class="form-control @error('email') is-invalid @enderror"
                    name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="mb-3 text-start">
                <label for="inputPassword" class="form-label">Password</label>
                <input type="password" class="form-control" id="inputPassword" name="password" data-toggle="password" required>
            </div>

            <!-- Remember Me -->
            <div class="mb-3 text-start">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                    <span class="ml-2">{{ __('Remember me') }}</span>
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary">Sign in</button>
        </form>

        <!-- Sign Up Link -->
        <div class="mt-4">
            Don't have an account yet? Sign up for free <a href="{{ route('signup') }}">here</a>!
        </div>

        <!-- Forgot Password -->
        <div class="mt-3">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm text-muted">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>
    </div>
@endsection
