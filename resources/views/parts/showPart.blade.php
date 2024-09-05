<div class="container-fluid">
    <br>
    <div class="row justify-content-between pe-3">
        <div class="col">
            <h4>{{ $part['part_name'] }}</h4>
            <h5>Total stock:
                {{ $total_stock }}
            </h5>
        </div>
        <div class="col d-flex justify-content-end">
            <a href="" id="mainPictureLink" data-toggle="lightbox" data-gallery="1">
                <img id="mainPicture" src="" alt="" style="max-width: 100%; height: auto;">
            </a>
        </div>
    </div>

    <!-- Parts Tabs -->
    <x-tablist id="partTabs" tabId1="{{ $tabId1 }}">
        <x-tab-button id="{{ $tabId1 }}" toggleTarget="{{ $tabToggleId1 }}" tabText="{{ $tabText1 }}" />
        <x-tab-button id="{{ $tabId2 }}" toggleTarget="{{ $tabToggleId2 }}" tabText="{{ $tabText2 }}" />
    </x-tablist>

    <!-- Tabs Content -->
    <div class="tab-content" id="partTabsContent">
        {{-- Info Tab --}}
        <div class="tab-pane fade" id="{{ $tabToggleId1 }}" role="tabpanel" tabindex="0">
            <br>
            <!-- Location / Quantity Table -->
            @include('parts.stockTable')


            <!-- Stock movement buttons -->
            <div class="input-group">
                <input type="text" class="form-control form-control-sm" placeholder="Stock:" disabled readonly>
                <button type="button" class="btn btn-sm btn-outline-primary" id="addStockButton">Add</button>
                <button type="button" class="btn btn-sm btn-outline-primary" id="moveStockButton">Move</button>
                <button type="button" class="btn btn-sm btn-outline-primary" id="reduceStockButton">Reduce</button>
            </div>
            <br><br>

            <h5>Part of:</h5>

            @include('parts.bomTable')

            <br>
            {{-- Datasheets --}}
            <div class="row justify-content-between pe-3">
                <div class="col h5">Datasheet</div>
                <div class="col-1"><button class="btn btn-sm btn-outline-primary" type="button"
                        data-bs-toggle="collapse" data-bs-target="#documentUploadContainer">
                        +</button>
                </div>
            </div>
            <x-upload-form containerId="documentContainer" uploadContainerId="documentUploadContainer"
                formId="documentUploadForm" inputId="document" inputName="document" labelText="Select Document"
                buttonText="Upload PDF" loadingId="documentLoadingContainer" headerText="Upload Datasheet / Document"
                acceptType=".pdf" />
            <br>
            {{-- Images --}}
            <div class="row justify-content-between pe-3">
                <div class="col h5">Images</div>
                <div class="col-1"><button class="btn btn-sm btn-outline-primary" type="button"
                        data-bs-toggle="collapse" data-bs-target="#imageUploadContainer">
                        +</button>
                </div>
                <x-upload-form containerId="imageContainer" uploadContainerId="imageUploadContainer"
                    formId="imageUploadForm" inputId="image" inputName="image" labelText="Select Image"
                    buttonText="Upload" loadingId="imageLoadingContainer" headerText="Upload Images"
                    acceptType="image/*" />
            </div>
        </div>
        {{-- Stock History Tab --}}
        <div class="tab-pane fade" id="{{ $tabToggleId2 }}" role="tabpanel" tabindex="0">
            test
            @include('parts.stockHistoryTable')

        </div>
    </div>
</div>
