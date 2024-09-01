<!-- resources/views/components/upload-form.blade.php -->
<div id="{{ $containerId }}"></div>
<br>
<div class="container px-0">
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">{{ $headerText }}</div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" id="{{ $formId }}">
                        @csrf
                        <div class="mb-3">
                            <label for="{{ $inputId }}" class="form-label form-label-sm">{{ $labelText }}</label>
                            <input type="file" class="form-control" id="{{ $inputId }}" name="{{ $inputName }}" accept="{{ $acceptType }}" required>
                        </div>
                        <div class="d-flex align-items-center">
                            <button type="submit" class="btn btn-sm btn-primary">{{ $buttonText }}</button>
                            <div id="{{ $loadingId }}" class="ms-2" style="display: none;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
