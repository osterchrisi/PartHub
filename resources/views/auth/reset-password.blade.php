@extends('centered-layout')

@section('content')
    <table class="table table-borderless mx-auto w-auto" style="borders: false;">
        <thead>
            <tr>
                <th>
                    <h4>Reset Your Password</h4>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <form method="POST" action="{{ route('password.store') }}">
                        @csrf

                        <!-- Password Reset Token -->
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                        <!-- Email Address -->
                        <div class="form-group">
                            <label for="email">{{ __('Email') }}</label>
                            <input id="email" class="form-control" type="email" name="email"
                                value="{{ old('email', $request->email) }}" required autofocus autocomplete="username">
                            @if ($errors->has('email'))
                                <div class="text-danger mt-2">{{ $errors->first('email') }}</div>
                            @endif
                        </div>

                        <!-- Password -->
                        <div class="form-group mt-4">
                            <label for="password">{{ __('New Password') }}</label>
                            <input id="password" class="form-control" type="password" name="password" required
                                autocomplete="new-password">
                            @if ($errors->has('password'))
                                <div class="text-danger mt-2">{{ $errors->first('password') }}</div>
                            @endif
                        </div>

                        <!-- Confirm Password -->
                        <div class="form-group mt-4">
                            <label for="password_confirmation">{{ __('Confirm Password') }}</label>
                            <input id="password_confirmation" class="form-control" type="password"
                                name="password_confirmation" required autocomplete="new-password">
                            @if ($errors->has('password_confirmation'))
                                <div class="text-danger mt-2">{{ $errors->first('password_confirmation') }}</div>
                            @endif
                        </div>

                        <div class="form-group mt-4 text-center">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Reset Password') }}
                            </button>
                        </div>
                    </form>
                </td>
            </tr>
        </tbody>
    </table>
@endsection
