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
        <x-tab-button id="bomsTab1" toggleTarget="bomInfo" tabText="Info" />
        <x-tab-button id="bomsTab2" toggleTarget="bomBuildHistory" tabText="Build History" />
    </ul>

    <!-- Tabs Content -->
    <div class="tab-content" id="bomsTabsContent">
        <div class="tab-pane fade" id="bomInfo" role="tabpanel" tabindex="1">
            <br>
            dis info
        </div>

        <div class="tab-pane fade" id="bomBuildHistory" role="tabpanel" tabindex="0">
            <br>
            dis history
        </div>
    </div>
</div>

<!-- Include custom JS -->
<script>
    $(document).ready(function() {
        loadActiveTab('boms', 'bomsTab1');
        addActiveTabEventListeners('boms');
    });
</script>
