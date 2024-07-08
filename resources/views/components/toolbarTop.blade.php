<div class="container-fluid px-0 sticky-top" style="z-index: 2;">
    <div class="bg-gradient d-flex align-items-center toolbar-top" style="background-color: rgba(0, 75, 145, 0.85)">
        <div class="pb-0 mb-0 pt-2 ps-3">
            <h4 class="toolbar-title">{{ $title }}</h4>
        </div>
        <ul class="nav px-2">
            <li class="nav-item p-1">
                <button type="button" class="btn btn-sm btn-primary btn-labeled" id="toolbarAddButton" style="background-color: rgb(43,87,153); border-color: rgb(43,87,153)"><span
                        class="btn-label"><i class="fas fa-lg fa-plus"></i></span>Add</button>
            </li>
            <li class="nav-item p-1">
                <button type="button" class="btn btn-sm btn-primary btn-labeled" id="toolbarDeleteButton" style="background-color: rgb(43,87,153); border-color: rgb(43,87,153)"><span
                        class="btn-label"><i class="fas fa-lg fa-trash"></i></span>Delete</button>
            </li>
            <li class="nav-item p-1">
                <button type="button" class="btn btn-sm btn-primary btn-labeled" data-bs-toggle="popover"
                    data-bs-title="Edit Part Detail"
                    data-bs-content="To edit any detail, just double click an editable cell in the table." style="background-color: rgb(43,87,153); border-color: rgb(43,87,153)"><span
                        class="btn-label"><i class="fas fa-lg fa-pen"></i></span>Edit</button>
            </li>
            <li class="nav-item p-1">
                <button type="button" class="btn btn-sm btn-primary btn-labeled" data-bs-toggle="collapse"
                    data-bs-target="#parts-filter-form" style="background-color: rgb(43,87,153); border-color: rgb(43,87,153)"><span class="btn-label"><i
                            class="fas fa-lg fa-filter"></i></span>Filter</button>
            </li>
            @yield('page specific buttons')
        </ul>
    </div>
</div>
