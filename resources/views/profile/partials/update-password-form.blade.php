<section>
    <header>
        <h2>
            {{ __('Update Password') }}
        </h2>

        <p>
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div class="col-3">
            <x-input-label for="current_password" :value="__('Current Password')" />
            <input id="current_password" name="current_password" type="password" class="form-control"
                autocomplete="current-password">
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div class="col-3">
            <x-input-label for="password" :value="__('New Password')" />
            <input id="password" name="password" type="password" class="form-control" autocomplete="new-password">
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div class="col-3">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <input id="password_confirmation" name="password_confirmation" type="password" class="form-control"
                autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="col-1 mt-3">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'password-updated')
                <div class="alert alert-success mt-3 p-2 pe-0">{{ __('Saved.') }}</div>
            @endif
            @if (session('status') === 'password-demo-change')
                <div class="alert alert-danger mt-3 p-2 pe-0">{{ __("Can't change demo user data") }}</div>
            @endif
        </div>
    </form>
</section>
