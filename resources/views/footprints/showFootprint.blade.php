<div class="container-fluid">
    <br>
    <h4>
        {{ $footprint_name }}
    </h4>
    <h5>
        {{ $footprint_alias }}
    </h5>

    <!-- Parts Tabs -->
    <ul class="nav nav-tabs" id="footprintsTabs" role="tablist">
        <x-tab-button id="{{ $tabId1 }}" toggleTarget="{{ $tabToggleId1 }}" tabText="{{ $tabText1 }}" />
        <x-tab-button id="{{ $tabId2 }}" toggleTarget="{{ $tabToggleId2 }}" tabText="{{ $tabText2 }}" />
    </ul>

    <!-- Tabs Content -->
    @php
    // print_r($parts_with_footprint);
    @endphp
    <div class="tab-content" id="bomsTabsContent">
        <div class="tab-pane fade" id="{{ $tabToggleId1 }}" role="tabpanel" tabindex="0">
            <br>
            <h5>Parts with this Footprint</h5>
            {{-- @include('footprints.footprintDetailsTable') --}}
            @php
            $table = \buildHTMLTable($db_columns, $nice_columns, $parts_with_footprint);
            echo $table;
            @endphp
        </div>

        <div class="tab-pane fade" id="{{ $tabToggleId2 }}" role="tabpanel" tabindex="0">
            <br>
            No footprint history
        </div>
    </div>
</div>