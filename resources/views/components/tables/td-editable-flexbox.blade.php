@props([
    'content' => '',
    'alignment' => 'left',
    'extraContentClass' => '',
])

<div class="d-flex justify-content-between w-100" id="editable-cell-content">
    @if ($alignment === 'right')
        <span class="edit-pen me-2">
            {{ $slot ?? '' }}
        </span>
        <span id="contentSpan" class="ms-auto {{ $extraContentClass }}">{{ $content }}</span>
    @else
        <span id="contentSpan" class="{{ $extraContentClass }}">{{ $content }}</span>
        <span class="edit-pen ms-2">
            {{ $slot ?? '' }}
        </span>
    @endif
</div>
