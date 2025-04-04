<!-- resources/views/components/upload-form.blade.php -->
<div id="{{ $containerId }}"></div>
<br>
<div class="container px-0 collapse" id="{{ $uploadContainerId }}">
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">{{ $headerText }}</div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" id="{{ $formId }}">
                        @csrf
                        <div class="input-group mb-3">
                            <input type="file" class="form-control form-control-sm" id="{{ $inputId }}"
                                name="{{ $inputName }}" accept="{{ $acceptType }}" required>
                            <button type="submit" class="btn btn-outline-primary btn-sm">{{ $buttonText }}</button>
                            <div id="{{ $loadingId }}" class="ms-2" style="display: none;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted ms-1">Allowed file types: {{ $acceptType }}. Max size: 2MB.</small>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
