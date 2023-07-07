<div class="container-fluid">
    <br>
    <h4>
        {{ $bom_name }}
    </h4>
    <h5>
        {{ $bom_description }}
    </h5>

    <!-- Parts Tabs -->
    <x-tablist id="bomsTabs" tabId1="{{ $tabId1 }}">
        <x-tab-button id="{{ $tabId1 }}" toggleTarget="{{ $tabToggleId1 }}" tabText="{{ $tabText1 }}" />
        <x-tab-button id="{{ $tabId2 }}" toggleTarget="{{ $tabToggleId2 }}" tabText="{{ $tabText2 }}" />
    </x-tablist>

    <!-- Tabs Content -->
    <div class="tab-content" id="bomsTabsContent">
        <div class="tab-pane fade" id="{{ $tabToggleId1 }}" role="tabpanel" tabindex="0">
            <br>
            @include('boms.bomDetailsTable')
        </div>

        <div class="tab-pane fade" id="{{ $tabToggleId2 }}" role="tabpanel" tabindex="0">
            <br>
            dis history
        </div>
    </div>
</div>