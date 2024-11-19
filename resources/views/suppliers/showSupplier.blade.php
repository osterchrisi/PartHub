<div class="container-fluid">
    <br>
    <h4>
        {{ $supplier->supplier_name }}
    </h4>
    <h5>
        {{ $supplier->supplier_alias }}
    </h5>

    <!-- Tabs -->
    <x-tablist id="supplierTabs" defaultTab="{{ $tabId1 }}">
        <x-buttons.tab-button id="{{ $tabId1 }}" toggleTarget="{{ $tabToggleId1 }}" tabText="{{ $tabText1 }}" />
        {{-- <x-buttons.tab-button id="{{ $tabId2 }}" toggleTarget="{{ $tabToggleId2 }}" tabText="{{ $tabText2 }}" /> --}}
    </x-tablist>

    <!-- Tabs Content -->
    <div class="tab-content" id="bomTabsContent">
        <div class="tab-pane fade" id="{{ $tabToggleId1 }}" role="tabpanel" tabindex="0">
            <br>
            <h5>Parts from this Supplier</h5>
            @include('suppliers.supplierDetailsTable')
        </div>

        {{-- <div class="tab-pane fade" id="{{ $tabToggleId2 }}" role="tabpanel" tabindex="0">
            <br>
            No supplier history
        </div> --}}
    </div>
</div>
