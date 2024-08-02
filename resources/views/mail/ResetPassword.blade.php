<x-email.layout>
    @slot('header')
        Reset Password
    @endslot

    <p class="lead">You are receiving this email because we received a password reset request for your account.</p>

    <div style="text-align: center;">
        <a href="{{ $resetUrl }}" style="display: inline-block; padding: 10px 20px; font-size: 16px; color: #fff; background-color: #007bff; border-radius: 5px; text-decoration: none;">{{ __('Reset Password') }}</a>
    </div>

    <p class="lead">If you did not request a password reset, no further action is required.</p>

    <p class="lead">Regards,<br>{{ config('app.name') }}</p>
</x-email.layout>
