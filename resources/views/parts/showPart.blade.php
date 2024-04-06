<?php
// Debug
// echo '<pre>';
// print_r($part);
// print_r($total_stock);
// print_r($bom_list);
// print_r($bomTableHeaders);
// print_r($stock_history);
// echo '</pre>';
?>

<div class="container-fluid">
    <br>
    <h4>
        {{ $part['part_name'] }}
    </h4>
    <h5>Total stock:
        {{ $total_stock }}
    </h5>

    <!-- Parts Tabs -->
    <x-tablist id="partsTabs" tabId1="{{ $tabId1 }}">
        <x-tab-button id="{{ $tabId1 }}" toggleTarget="{{ $tabToggleId1 }}" tabText="{{ $tabText1 }}" />
        <x-tab-button id="{{ $tabId2 }}" toggleTarget="{{ $tabToggleId2 }}" tabText="{{ $tabText2 }}" />
    </x-tablist>

    <!-- Tabs Content -->
    <div class="tab-content" id="partsTabsContent">
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
            <h5>Datasheet:</h5>
            <br>
            <h5>Image:</h5>
            <div id="imageContainer"></div>
            <br>
            <div class="container px-0">
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-header">Upload Image</div>

                            <div class="card-body">
                                <form
                                    action="{{ route('upload-image', ['type' => 'part', 'id' => $part['part_id']]) }}"
                                    method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <div class="mb-3">
                                        <label for="image" class="form-label form-label-sm">Select Image</label>
                                        <input type="file" class="form-control" id="image" name="image">
                                    </div>

                                    <button type="submit" class="btn btn-sm btn-primary">Upload</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        {{-- Stock History Tab --}}
        <div class="tab-pane fade" id="{{ $tabToggleId2 }}" role="tabpanel" tabindex="0">

            @include('parts.stockHistoryTable')

        </div>
    </div>
</div>

<script>
    // Fetch images associated with the given type and ID

    function fetchImages(type, id) {
        $.ajax({
            url: "{{ route('part.images', ['id' => ':id', 'type' => ':type']) }}".replace(':id', id).replace(':type', type),
            type: 'GET',
            data: {
                type: type,
                id: id
            },
            success: function(response) {
                // Check if images exist
                if (response.length > 0) {
                    // Loop through images and append them to a container
                    response.forEach(function(image) {
                        $('#imageContainer').append('<img src="' + image.filename +
                            '" alt="Image">');
                    });
                }
            }
        });
    }

    // Call fetchImages function with the current part type and ID
    var currentPartType = "part"; // Change this to the appropriate type
    var currentPartId = {{ $part['part_id'] }};
    fetchImages(currentPartType, currentPartId);
</script>
