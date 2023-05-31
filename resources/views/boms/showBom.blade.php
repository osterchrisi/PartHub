<?php
// Debug
// echo '<pre>';
// print_r($bom_elements);
// echo '</pre>';
?>

<div class="container-fluid">
    <br>
    <h4>
        {{ $bom_name }}
    </h4>
    <h5>
        {{ $bom_description }}
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
            @include('boms.bomDetailsTable')
        </div>

        <div class="tab-pane fade" id="{{ $tabToggleId2 }}" role="tabpanel" tabindex="0">
            <br>
            dis history
        </div>
    </div>
</div>

<!-- Include custom JS -->
<script>
    $(document).ready(function() {
        loadActiveTab('boms', '{{ $tabId1 }}');
        addActiveTabEventListeners('boms');

        bootstrapBomDetailsTable();
        inlineProcessing();

        // Allow extra HTML elements for the popover mini stock table
        var myDefaultAllowList = bootstrap.Tooltip.Default.allowList

        // Allow table elements
        myDefaultAllowList.table = []
        myDefaultAllowList.thead = []
        myDefaultAllowList.tr = []
        myDefaultAllowList.td = []
        myDefaultAllowList.tbody = []

        // Allow td elements and data-bs-option attributes on td elements
        myDefaultAllowList.td = ['data-bs-option']

        // Initialize all popovers
        popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
        popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));

        // Re-initialize the popovers after toggling a column
        //* This should be possible via the 'column-switch.bs.table' but it never fires...
        $(function() {
            $('#BomDetailsTable').on('post-body.bs.table', function() {
                popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
                popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap
                    .Popover(popoverTriggerEl));
            });
        });
    });
</script>
