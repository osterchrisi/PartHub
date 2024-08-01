<x-email.layout>
    @slot('header')
        {{-- Hey, {{ $greeting ?? 'Hello!' }} --}}
        Hey, du!
    @endslot

    @foreach ($introLines as $line)
        <p class="lead">{{ $line }}</p>
    @endforeach

    @isset($actionText)
        <p class="lead"><a href="{{ $actionUrl }}" class="button">{{ $actionText }}</a></p>
    @endisset

    @foreach ($outroLines as $line)
        <p class="lead">{{ $line }}</p>
    @endforeach

    <p>{{ $salutation ?? 'Regards,' }}<br>{{ config('app.name') }}</p>

    @isset($actionText)
        <p class="lead">If you're having trouble clicking the "{{ $actionText }}" button, copy and paste the URL below into your web browser: <a href="{{ $actionUrl }}">{{ $displayableActionUrl }}</a></p>
    @endisset
</x-email.layout>