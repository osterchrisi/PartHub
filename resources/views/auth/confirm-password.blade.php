@extends('centered-layout')

@section('content')
    <table class="table table-borderless mx-auto w-auto" style="borders: false;">
        <thead>
            <tr>
                <th>
                    <h4>Confirm Your Password</h4>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
                    </div>

                    <form method="POST" action="{{ route('password.confirm') }}">
                        @csrf

                        <!-- Password -->
                        <div class="form-group">
                            <label for="password">{{ __('Password') }}</label>
                            <input id="password" class="form-control" type="password" name="password" required
                                autocomplete="current-password">
                            @if ($errors->has('password'))
                                <div class="text-danger mt-2">{{ $errors->first('password') }}</div>
                            @endif
                        </div>

                        <div class="form-group mt-4 text-center">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Confirm') }}
                            </button>
                        </div>
                    </form>
                </td>
            </tr>
        </tbody>
    </table>
@endsection
