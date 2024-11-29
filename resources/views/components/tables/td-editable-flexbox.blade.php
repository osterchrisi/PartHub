@props([
    'content' => '',
    'alignment' => 'left',
    'extraContentClass' => '',
])

<div class="d-flex justify-content-between w-100" id="editable-cell-content">
    @if ($alignment === 'right')
        <span class="me-2">
            {{ $slot ?? '' }}
        </span>
        <span id="contentSpan" class="ms-auto {{ $extraContentClass }}">
            @if (filter_var($content, FILTER_VALIDATE_URL))
                <a href="{{ $content }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                    {{ $content }}
                </a>
            @else
                {{ $content }}
            @endif
        </span>
    @else
        <span id="contentSpan" class="{{ $extraContentClass }}">
            @if (filter_var($content, FILTER_VALIDATE_URL))
                <a href="{{ $content }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                    {{ $content }}
                </a>
            @else
                {{ $content }}
            @endif
        </span>
        <span class="ms-2">
            {{ $slot ?? '' }}
        </span>
    @endif
</div>
