<div class="container-fluid px-0 sticky-top" style="z-index: 2;">
    <div class="bg-gradient d-flex align-items-center toolbar-top">
        <div class="pb-0 mb-0 pt-2 ps-3">
            <h4 class="toolbar-title">{{ $title }}</h4>
        </div>
        <ul class="nav px-2">
            @if($showAddButton ?? false)
                <x-toolbar-button icon="fa-plus" text="Add" id="toolbarAddButton" />
            @endif

            @if($showDeleteButton ?? false)
                <x-toolbar-button icon="fa-trash" text="Delete" id="toolbarDeleteButton" />
            @endif

            @if($showEditButton ?? false)
                <x-toolbar-button icon="fa-pen" text="Edit" data-bs-toggle="popover" data-bs-title="Edit Part Detail"
                    data-bs-content="To edit any detail, just double click an editable cell in the table." />
            @endif

            @if($showFilterButton ?? false)
                <x-toolbar-button icon="fa-filter" text="Filter" id="toolbarFilterButton" data-bs-toggle="collapse"
                    data-bs-target="#parts-filter-form" />
            @endif

            @if($showAssembleButton ?? false)
            <x-toolbar-button icon="fa-wrench" text="Assemble" id="toolbarAssembleBomButton" />
        @endif

        </ul>
    </div>
</div>
