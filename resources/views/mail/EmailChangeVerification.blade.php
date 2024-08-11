<x-email.layout>
    @slot('header')
        Hey!
    @endslot

    <p class="lead">We received a request to change your account e-mail. Please click the button below to verify your new email address.</p>

    <div style="text-align: center;">
        <a href="{{ $actionUrl }}"
            style="display: inline-block; padding: 10px 20px; font-size: 16px; color: #fff; background-color: #007bff; border-radius: 5px; text-decoration: none;">{{ $actionText }}</a>
    </div>

    @isset($outroLines)
        @foreach ($outroLines as $line)
            <p class="lead">{{ $line }}</p>
        @endforeach
    @endisset

    <p class="lead">Thanks and all the best,<br>
        The PartHub Team from Berlin</p>
    <img src="{{ env('APP_FAVICON') }}" alt="PartHub Logo" style="width: 50px; height: 50px;">
</x-email.layout>
