@props(['messages'])

@if ($messages)
    <div class="mx-0 px-0 my-0 px-0 alert alert-danger">
        <ul {{ $attributes->merge(['class' => 'text-sm']) }}>
            @foreach ((array) $messages as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    </div>
@endif
