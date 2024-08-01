@include('header')
@include('navbar')

<div class="d-flex flex-grow-1 justify-content-center align-items-center">
    <div class="greeting d-flex align-items-center">
        <div class="row">
            <div class="col-3">
            </div>
            <div class="col-6">
                <table class="table table-borderless text-center mx-auto w-auto" style="borders: false;">
                    <thead>
                        <tr>
                            <th>
                                <h4>Verify Your Email Address</h4>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
                            </td>
                        </tr>
                        @if (session('status') == 'verification-link-sent')
                            <tr>
                                <td>
                                    <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                                        {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                                    </div>
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td style='text-align:center'>
                                <form method="POST" action="{{ route('verification.send') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Resend Verification Email') }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-link text-decoration-none text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                        {{ __('Log Out') }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-3">
            </div>
        </div>
    </div>
</div>
