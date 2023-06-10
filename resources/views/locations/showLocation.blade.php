<div class="container-fluid">
    <br>
    <h4>
        {{ $location_name }}
    </h4>
    <h5>
        {{ $location_description }}
    </h5>

    <!-- Parts Tabs -->
    <ul class="nav nav-tabs" id="bomsTabs" role="tablist">
        <x-tab-button id="{{ $tabId1 }}" toggleTarget="{{ $tabToggleId1 }}" tabText="{{ $tabText1 }}" />
        <x-tab-button id="{{ $tabId2 }}" toggleTarget="{{ $tabToggleId2 }}" tabText="{{ $tabText2 }}" />
    </ul>

    <!-- Tabs Content -->
    <div class="tab-content" id="bomsTabsContent">
        <div class="tab-pane fade" id="{{ $tabToggleId1 }}" role="tabpanel" tabindex="0">
            <br>
            <h5>Stock in this Location</h5>
            {{-- @include('locations.locationDetailsTable') --}}
            @php
            $table = \buildHTMLTable($db_columns, $nice_columns, $stock_in_location);
            echo $table;
            @endphp
        </div>

        <div class="tab-pane fade" id="{{ $tabToggleId2 }}" role="tabpanel" tabindex="0">
            <br>
            dis history
        </div>
    </div>
</div>