<div class="container-fluid px-0 sticky-top" style="z-index: 2;">
    <div class="bg-info bg-gradient d-flex align-items-center ">
        <div class="pb-0 mb-0 pt-2 ps-3">
            <h4>{{ $title }}</h4>
        </div>
        <ul class="nav px-2">
            <li class="nav-item p-1">
                <button type="button" class="btn btn-sm btn-primary btn-labeled" id="toolbarAddButton"><span
                        class="btn-label"><i class="fas fa-lg fa-plus"></i></span>Add</button>
            </li>
            <li class="nav-item p-1">
                <button type="button" class="btn btn-sm btn-primary btn-labeled" id="toolbarDeleteButton"><span
                        class="btn-label"><i class="fas fa-lg fa-trash"></i></span>Delete</button>
            </li>
            <li class="nav-item p-1">
                <button type="button" class="btn btn-sm btn-primary btn-labeled" data-bs-toggle="popover"
                    data-bs-title="Edit Part Detail"
                    data-bs-content="To edit any detail, just double click an editable cell in the table."><span
                        class="btn-label"><i class="fas fa-lg fa-pen"></i></span>Edit</button>
            </li>
            <li class="nav-item p-1">
                <button type="button" class="btn btn-sm btn-primary btn-labeled" data-bs-toggle="collapse"
                    data-bs-target="#parts-filter-form"><span class="btn-label"><i
                            class="fas fa-lg fa-filter"></i></span>Filter</button>
            </li>
        </ul>
    </div>
</div>
