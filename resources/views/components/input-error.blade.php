@props(['messages'])

@if ($messages)
    <div class="mx-0 my-0 alert alert-danger pt-3 pb-0 ps-0">
        <ul {{ $attributes->merge(['class' => 'text-sm']) }}>
            @foreach ((array) $messages as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    </div>
@endif
