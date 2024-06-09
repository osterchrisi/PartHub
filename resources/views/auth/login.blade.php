@include('header')
@include('navbar')

<div class="d-flex flex-grow-1 justify-content-center align-items-center">
    <div class="greeting d-flex align-items-center">
        <table class="table table-borderless text-center mx-auto w-auto" style="borders: false">
            <thead>
                <tr>
                    <th>
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
                            <input type="email" class="form-control" id="inputEmail" name="email" required>
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
    </div>
</div>
