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
        <x-buttons.tab-button id="{{ $tabId4 }}" toggleTarget="{{ $tabToggleId4 }}"
            tabText="{{ $tabText4 }}" />
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
</div>