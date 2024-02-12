<div class="container-fluid">
    <br>
    <h4>
        {{ $footprint_name }}
    </h4>
    <h5>
        {{ $footprint_description }}
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
            <h5>Parts with this Footprint</h5>
            {{-- @include('footprints.footprintDetailsTable') --}}
            @php
            $table = \buildHTMLTable($db_columns, $nice_columns, $stock_in_footprint);
            echo $table;
            @endphp
        </div>

        <div class="tab-pane fade" id="{{ $tabToggleId2 }}" role="tabpanel" tabindex="0">
            <br>
            dis history
        </div>
    </div>
</div>