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
        <span id="contentSpan" class="ms-auto {{ $extraContentClass }}">{{ $content }}</span>
    @else
        <span id="contentSpan" class="{{ $extraContentClass }}">{{ $content }}</span>
        <span class="ms-2">
            {{ $slot ?? '' }}
        </span>
    @endif
</div>
