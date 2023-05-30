<button
    {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-primary btn-sm']) }}>
    {{ $slot }}
</button>
