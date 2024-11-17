<div class="modal fade" id="{{ $modalId }}" data-bs-backdrop="{{ $backdrop }}"
    data-bs-keyboard="{{ $keyboard }}" tabindex="-1">
    <div class="modal-dialog {{ $size }}">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="{{ $modalTitleId }}">{{ $title }}</h1>
                <button type="button" class="btn-close" id="{{ $cancelButton1Id ?? '' }}" data-bs-dismiss="modal"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body mx-1">
                {{ $slot }}
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="{{ $cancelButton2Id ?? '' }}" data-bs-dismiss="modal">{{ $cancelText }}</button>
                @if (!empty($submitButtonId))
                <button type="submit" class="btn btn-primary" id="{{ $submitButtonId }}">{{ $submitText }}</button>
                @endif
            </div>
        </div>
    </div>
</div>
