@include('parts.mouserSearch')
<!-- Location / Quantity Table -->
@include('parts.stockTable')

<!-- Stock movement buttons -->
<div class="input-group mb-4">
    <input type="text" class="form-control form-control-sm" placeholder="Stock:" disabled readonly>
    <button type="button" class="btn btn-sm btn-outline-primary" id="addStockButton">Add</button>
    <button type="button" class="btn btn-sm btn-outline-primary" id="moveStockButton">Move</button>
    <button type="button" class="btn btn-sm btn-outline-primary" id="reduceStockButton">Reduce</button>
</div>

<h5>Part of:</h5>

@include('parts.bomTable')

<br>
{{-- Datasheets --}}
<div class="row justify-content-between pe-1">
    <div class="col h5">Datasheet</div>
    <div class="col-1 d-flex justify-content-end py-2"><button class="btn btn-extra-sm btn-outline-primary" type="button"
            data-bs-toggle="collapse" data-bs-target="#documentUploadContainer">
            <i class="fas fa-s fa-plus icon-extra-small"></i></button>
    </div>
</div>
<x-upload-form containerId="documentContainer" uploadContainerId="documentUploadContainer"
    formId="documentUploadForm" inputId="document" inputName="document" labelText="Select Document"
    buttonText="Upload PDF" loadingId="documentLoadingContainer" headerText="Upload Datasheet / Document"
    acceptType=".pdf" />
<br>
{{-- Images --}}
<div class="row justify-content-between pe-1">
    <div class="col h5">Images</div>
    <div class="col-1  d-flex justify-content-end py-2"><button class="btn btn-extra-sm btn-outline-primary" type="button"
            data-bs-toggle="collapse" data-bs-target="#imageUploadContainer">
            <i class="fas fa-s fa-plus icon-extra-small"></i></button>
    </div>
    <x-upload-form containerId="imageContainer" uploadContainerId="imageUploadContainer"
        formId="imageUploadForm" inputId="image" inputName="image" labelText="Select Image"
        buttonText="Upload" loadingId="imageLoadingContainer" headerText="Upload Images"
        acceptType="image/*" />
</div>