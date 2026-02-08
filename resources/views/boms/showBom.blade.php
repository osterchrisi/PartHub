<div class="container-fluid">
    <br>
    <h4>
        {{ $bom_name }}
    </h4>
    <h5>
        {{ $bom_description }}
    </h5>

    <!-- Parts Tabs -->
    <x-tablist id="bomTabs" defaultTab="{{ $tabId1 }}">
        <x-buttons.tab-button id="{{ $tabId1 }}" toggleTarget="{{ $tabToggleId1 }}" tabText="{{ $tabText1 }}" />
        <x-buttons.tab-button id="{{ $tabId2 }}" toggleTarget="{{ $tabToggleId2 }}" tabText="{{ $tabText2 }}" />
    </x-tablist>

    <!-- Tabs Content -->
    <div class="tab-content" id="bomTabsContent">
        <div class="tab-pane fade" id="{{ $tabToggleId1 }}" role="tabpanel" tabindex="0">
            <br>
            {{-- BOM Edit Toolbar --}}
            <div class="d-flex align-items-center gap-2 mb-2" id="bomEditorToolbar" data-bom-id="{{ $bom_id }}">
                <button class="btn btn-sm btn-outline-primary" id="bomEditBtn" type="button">
                    Edit BOM
                </button>

                <button class="btn btn-sm btn-secondary d-none" id="bomEditCancelBtn" type="button">
                    Cancel
                </button>

                <button class="btn btn-sm btn-primary d-none" id="bomEditSaveBtn" type="button">
                    Save
                </button>

                <small class="text-muted ms-auto d-none" id="bomEditHint">
                    Editing is local until you hit Save.
                </small>
            </div>

            {{-- BOM Editor Container (hidden by default, shown when editing) --}}
            <div id="bomEditorContainer" class="d-none">
                {{-- Editor UI will be rendered here by JS --}}
            </div>

            {{-- Non-editable BOM table --}}
            <div id="bomDetailsViewContainer">
                @include('boms.bomDetailsTable')
            </div>

        </div>

        <div class="tab-pane fade" id="{{ $tabToggleId2 }}" role="tabpanel" tabindex="0">
            <br>
            @include('boms.bomRunsTable')
        </div>
    </div>
</div>
