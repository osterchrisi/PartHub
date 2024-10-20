<?php
use App\Http\Controllers\ProfileController;
$user = Auth::user();
?>

{{-- Parent Template --}}
@extends('centered-layout')

@section('content')
    <div class="row justify-content-center">
        <div class="col">
            <div class="card shadow-sm mt-4 mb-4">
                <div class="card-body">
                    <header>
                        <h2 class="card-title text-center">
                            {{ __('Profile Information') }}
                        </h2>

                        <small class="d-block text-muted text-center mb-3">
                            {{ __("Update your account's profile information and email address.") }}
                        </small>
                    </header>

                    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                        @csrf
                    </form>

                    <form method="post" action="{{ route('profile.update') }}" class="mt-6">
                        @csrf
                        @method('patch')

                        <div class="row">
                            <div class="col">
                                <x-input-label for="name" :value="__('Name')" />
                                <x-text-input id="name" name="name" type="text"
                                    class="form-control form-control-sm" :value="old('name', $user->name)" required autofocus
                                    autocomplete="name" />
                                <x-input-error class="alert" :messages="$errors->get('name')" />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" name="email" type="email"
                                    class="form-control form-control-sm" :value="old('email', $user->email)" required
                                    autocomplete="username" />
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
                            <div class="col">
                                <x-buttons.primary-button>{{ __('Save') }}</x-buttons.primary-button>

                                @if (session('status') === 'profile-updated')
                                    <div x-data="{ show: true }" class="alert alert-success mt-3 p-2 pe-0">
                                        {{ __('Saved!') }}</div>
                                @endif

                                @if (session('status') === 'email-updated')
                                    <div x-data="{ show: true }" class="alert alert-success mt-3 p-2 pe-0">
                                        {{ __('Your email address has been successfully changed.') }}</div>
                                @endif
                                @if (session('status') === 'email-changed')
                                    <div x-data="{ show: true }" class="alert alert-success mt-3 p-2 pe-0">
                                        {{ __('A verification link has been sent to your new email address.') }}</div>
                                @endif
                                @if (session('status') === 'profile-demo-change')
                                    <div x-data="{ show: true }" class="alert alert-danger mt-3 p-2 pe-0">
                                        {{ __("Can't change demo user data") }}</div>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card shadow-sm mt-4 mb-4">
                <div class="card-body">
                    <h2 class="card-title text-center">
                        {{ __('Update Password') }}
                    </h2>
                    <small class="d-block text-muted text-center mb-3">
                        {{ __('Ensure your account is using a long, random password to stay secure.') }}
                    </small>
                    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
                        @csrf
                        @method('put')
                        <div class="col">
                            <x-input-label for="current_password" :value="__('Current Password')" />
                            <input id="current_password" name="current_password" type="password" class="form-control form-control-sm"
                                autocomplete="current-password" data-toggle="password" data-size="sm">
                            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                        </div>
                        <div class="col">
                            <x-input-label for="password" :value="__('New Password')" />
                            <input id="password" name="password" type="password" class="form-control form-control-sm"
                                autocomplete="new-password" data-toggle="password" data-size="sm">
                            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                        </div>
                        <div class="col">
                            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                            <input id="password_confirmation" name="password_confirmation" type="password"
                                class="form-control form-control-sm" autocomplete="new-password" data-toggle="password" data-size="sm">
                            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                        </div>
                        <div class="col mt-3">
                            <x-buttons.primary-button>{{ __('Save') }}</x-buttons.primary-button>
                            @if (session('status') === 'password-updated')
                                <div class="alert alert-success mt-3 p-2 pe-0">{{ __('Saved.') }}</div>
                            @endif
                            @if (session('status') === 'password-demo-change')
                                <div class="alert alert-danger mt-3 p-2 pe-0">{{ __("Can't change demo user data") }}</div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
            <div class="card shadow-sm mt-4 mb-4">
                <div class="card-body">
                    <h2 class="card-title text-center">
                        {{ __('Delete Account') }}
                    </h2>
                    <p>
                        {!! nl2br(
                            __('Once your account is deleted, all of its resources and data will be permanently deleted.') .
                                "\n" .
                                __('Before deleting your account, please download any data or information that you wish to retain.'),
                        ) !!}
                    </p>
                    <x-buttons.danger-button x-data=""
                        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">{{ __('Delete Account') }}</x-buttons.danger-button>
                    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
                        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
                            @csrf
                            @method('delete')
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ __('Are you sure you want to delete your account?') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                            </p>
                            <div class="mt-6">
                                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />
                                <x-text-input id="password" name="password" type="password" class="mt-1 block w-3/4"
                                    placeholder="{{ __('Password') }}" />
                                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                            </div>
                            <div class="mt-6 flex justify-end">
                                <x-buttons.secondary-button x-on:click="$dispatch('close')">
                                    {{ __('Cancel') }}
                                </x-buttons.secondary-button>
                                <x-buttons.danger-button class="ml-3">
                                    {{ __('Delete Account') }}
                                </x-buttons.danger-button>
                            </div>
                        </form>
                    </x-modal>
                </div>
            </div>
        </div>
    </div>




@endsection

@section('modals and menus')
@endsection
