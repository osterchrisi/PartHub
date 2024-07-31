@props(['messages'])

@if ($messages)
    <div class="mx-0 my-2 alert alert-danger p-0">
        <ul {{ $attributes->merge(['class' => 'text-sm']) }}>
            @foreach ((array) $messages as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    </div>
@endif
