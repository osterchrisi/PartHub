<div class="container-fluid">
    <br>
    <div class="row justify-content-between pe-3">
        <div class="col">
            <h4>{{ $part['part_name'] }}</h4>
            <h6 class="text-muted">{{ $part['part_description'] }}</h6>
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

    <!-- Tabs -->
    <x-tablist id="partTabs" defaultTab="{{ $tabId1 }}">
        <x-buttons.tab-button id="{{ $tabId1 }}" toggleTarget="{{ $tabToggleId1 }}"
            tabText="{{ $tabText1 }}" />
        <x-buttons.tab-button id="{{ $tabId2 }}" toggleTarget="{{ $tabToggleId2 }}"
            tabText="{{ $tabText2 }}" />
        <x-buttons.tab-button id="{{ $tabId3 }}" toggleTarget="{{ $tabToggleId3 }}"
            tabText="{{ $tabText3 }}" />
    </x-tablist>

    <!-- Tabs Content -->
    <div class="tab-content" id="partTabsContent">
        {{-- Info Tab --}}
        <div class="tab-pane fade" id="{{ $tabToggleId1 }}" role="tabpanel" tabindex="0">

            @include('parts.partInfo')

        </div>
        {{-- Stock History Tab --}}
        <div class="tab-pane fade" id="{{ $tabToggleId2 }}" role="tabpanel" tabindex="0">

            @include('parts.stockHistoryTable')

        </div>

        {{-- Suppliers Tab --}}
        <div class="tab-pane fade" id="{{ $tabToggleId3 }}" role="tabpanel" tabindex="0">

            @include('parts.supplierDataTable')

        </div>
    </div>
    
    {{-- Part Comment --}}
    <div class="pb-3 pe-3 pt-3">
        <h6>Comment</h6>
        {{-- <p class="form-control bg-light">{{ $part['part_comment'] }}</p> --}}
        <table>
            <tr>
                <td data-editable="true" class="editable editable-text" data-id="{{ $part['part_id'] }}"
                    data-column="part_comment" data-table_name="parts" data-id_field="part_id">
                    <x-tables.td-editable-flexbox :content="$part['part_comment'] ?? ''">
                        {{-- Not yet working
                                <x-tables.copy-clipboard :content="$part[$column_data] ?? ''" /> --}}
                        <x-tables.edit-pen />
                    </x-tables.td-editable-flexbox>
                </td>
            </tr>
        </table>

    </div>



</div>
