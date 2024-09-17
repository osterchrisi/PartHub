<div class="container-fluid">
    <br>
    <h4>
        {{ $supplier_name }}
    </h4>
    <h5>
        {{ $supplier_alias }}
    </h5>

    <!-- Parts Tabs -->
    <ul class="nav nav-tabs" id="supplierTabs" role="tablist">
        <x-buttons.tab-button id="{{ $tabId1 }}" toggleTarget="{{ $tabToggleId1 }}" tabText="{{ $tabText1 }}" />
        <x-buttons.tab-button id="{{ $tabId2 }}" toggleTarget="{{ $tabToggleId2 }}" tabText="{{ $tabText2 }}" />
    </ul>

    <!-- Tabs Content -->
    @php
    // print_r($parts_from_supplier);
    @endphp
    <div class="tab-content" id="bomTabsContent">
        <div class="tab-pane fade" id="{{ $tabToggleId1 }}" role="tabpanel" tabindex="0">
            <br>
            <h5>Parts from this Supplier</h5>
            {{-- @include('suppliers.supplierDetailsTable') --}}
            @php
            $table = \buildHTMLTable($db_columns, $nice_columns, $parts_from_supplier);
            echo $table;
            @endphp
        </div>

        <div class="tab-pane fade" id="{{ $tabToggleId2 }}" role="tabpanel" tabindex="0">
            <br>
            No supplier history
        </div>
    </div>
</div>