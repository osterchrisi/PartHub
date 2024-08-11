<section>
    <header>
        <h2>
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="row">
            <div class="col-3">
                <x-input-label for="name" :value="__('Name')" />
                <x-text-input id="name" name="name" type="text" class="form-control form-control-sm"
                    :value="old('name', $user->name)" required autofocus autocomplete="name" />
                <x-input-error class="alert" :messages="$errors->get('name')" />
            </div>
        </div>

        <div class="row">
            <div class="col-3">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" name="email" type="email" class="form-control form-control-sm"
                    :value="old('email', $user->email)" required autocomplete="username" />
                <x-input-error class="alert" :messages="$errors->get('email')" />
                    
                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                    <div>
                        <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                            {{ __('Your email address is unverified.') }}

                            <button form="send-verification"
                                class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-3">
                <x-primary-button>{{ __('Save') }}</x-primary-button>

                @if (session('status') === 'profile-updated')
                    <div x-data="{ show: true }" class="alert alert-success mt-3 p-2 pe-0">{{ __('Saved!') }}</div>
                @endif

                @if (session('status') === 'email-updated')
                    <div x-data="{ show: true }" class="alert alert-success mt-3 p-2 pe-0">{{ __('Your email address has been successfully changed.') }}</div>
                @endif
                @if (session('status') === 'email-changed')
                <div x-data="{ show: true }" class="alert alert-success mt-3 p-2 pe-0">{{ __('A verification link has been sent to your new email address.') }}</div>
            @endif
            </div>
        </div>
    </form>
</section>
