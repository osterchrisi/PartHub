@extends('centered-layout')

@section('content')
    <table class="table table-borderless text-center mx-auto w-auto" style="borders: false">
        <thead>
            <tr>
                <th>
                    @if (session('loggedOut'))
                        <div class="alert alert-success">You've been successfully logged out 👍</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif

                    <h4>Log in to your PartHub account</h4>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style='text-align:left'>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <label for="inputEmail" class="form-label">Email</label>
                        <input id="inputEmail" type="email" class="form-control @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        <label for="inputPassword" class="form-label">Password</label>
                        <input type="password" class="form-control" id="inputPassword" name="password" required>
                </td>
            </tr>
            <tr>
                <td>
                    <button type="submit" class="btn btn-primary">Sign in</button>
                    </form>

                </td>
            </tr>
            <tr>
                <td>Don't have an account yet? Sign up for free <a href={{ route('signup') }}>here</a>!
                </td>
            </tr>
            <!-- Remember Me -->
            <tr>
                <td>
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox"
                            class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                            name="remember">
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
                    </label>
                </td>
            </tr>
            <tr>
                <td>
                    @if (Route::has('password.request'))
                        <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                            href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
                </td>
            </tr>
            <tr>
                <td>
                    <hr> OR
                    <hr>
                    <a href="{{ route('google.login') }}" class="btn btn-primary">
                        Continue with Google account
                    </a>
                </td>
            </tr>
            {{-- @unless (auth()->check())
                    <tr>
                        <td colspan="3">
                            <table class="table table-borderless">
                                <tbody class="alert alert-danger">
                                    <tr>
                                        <td>
                                            <form id="demoLoginButton" action="{{ route('demo.login') }}" method="GET">
                                                @csrf<button type="submit" class="btn btn-danger"
                                                    id="continueDemo">Continue as demo
                                                    user</button></form>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                @endunless --}}

        <tbody>
    </table>
@endsection
