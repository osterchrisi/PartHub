@props([
    'content' => '',
    'alignment' => 'left',
    'extraContentClass' => '',
])

<div class="d-flex justify-content-between w-100" id="editable-cell-content">
    {{-- Numbers for Couting --}}
    @if ($alignment === 'right')
        {{-- Icons --}}
        <span class="me-2 d-flex align-items-center">
            {{ $slot ?? '' }}
        </span>
        {{-- Content --}}
        <span id="contentSpan" class="ms-auto {{ $extraContentClass }}">
            {{-- URLs in the SupplierDataTable --}}
            @if (filter_var($content, FILTER_VALIDATE_URL))
                <a href="{{ $content }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                    {{ $content }}
                </a>
                {{-- All other content --}}
            @else
                {{ $content }}
            @endif
        </span>
    {{-- All other table cells --}}
    @else
        {{-- Content --}}
        <span id="contentSpan" class="{{ $extraContentClass }}">
            {{-- URLs in the SupplierDataTable --}}
            @if (filter_var($content, FILTER_VALIDATE_URL))
                <a href="{{ $content }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                    {{ $content }}
                </a>
                {{-- All other content --}}
            @else
                {{ $content }}
            @endif
        </span>
        {{-- Icons --}}
        <span class="ms-2 d-flex align-items-center">
            {{ $slot ?? '' }}
        </span>
    @endif
</div>
