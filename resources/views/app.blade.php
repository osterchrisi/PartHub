{{-- Header --}}
@include('header')

{{-- Navbar and Toolbar --}}
@include('navbar')
@include('components.toolbarTop')


{{-- Page Contents --}}
<div class="container-fluid pb-3" id="content-container">
    <br>
    <div class="row" id="content-row">
        {{-- Filter Form --}}
        <div class="row collapse" id="filter-form-div">
            @yield('filter-form')
        </div>
        @if (isset($view) && $view === 'parts')
            <div class="ps-2">
                <button type="button" class="categories-button btn btn-sm btn-outline-secondary mb-1 ms-1 me-3"
                    id="cat-show-btn">Categories</button>
            </div>
        @endif

        <div class="d-flex flex-nowrap w-100">
            {{-- Categories Section --}}
            @if (isset($view) && $view === 'parts')
                <div class="d-flex flex-column">
                    <div class="flex-grow-0 sticky" id='category-window-container' style="display: none; position: sticky; top: 50px;">
                        <div class="rounded border border-dark border-opacity-25 me-3 ps-3 pb-3 shadow-sm"
                            id="category-window">
                            @include('categories.categoriesTable')
                        </div>
                        <div class="category-resize-handle ui-resizable-handle ui-resizable-e rounded-1"></div>
                    </div>
                </div>
            @endif

            {{-- Table Window --}}
            <div class="col-10 flex-shrink-1" id="table-window-container" style="overflow-x: auto;">
                <div class="border rounded border-primary border-opacity-25 px-3 me-3 shadow-sm" id="table-window">
                    @yield('table-window')
                    <p class="text-muted key-hint"><small>Use <kbd>Ctrl</kbd>, <kbd>Shift</kbd> to select multiple rows
                            in the table</small></p>
                </div>
                <div class="table-resize-handle ui-resizable-handle ui-resizable-e rounded"></div>
            </div>

            {{-- Info Window --}}
            <div class='flex-grow-1 d-flex sticky justify-content-center info-window rounded border border-info border-opacity-25 pb-3 shadow-sm'
                id='info-window' style="position: sticky; top: 50px; height: 89vh; overflow-x: auto;">
                @yield('info-window')
            </div>
        </div>
    </div>
</div>


<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <span id="deleteQuestion">Are sure you want to delete this image?</span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                <button type="button" class="btn btn-primary" id="confirmDeleteButton">Yes</button>
            </div>
        </div>
    </div>
</div>

{{-- Inline Table Cell Updating Error Modal --}}
<x-modals.parthub-modal
    modalId="updateErrorModal"
    modalTitleId="updateErrorModalTitle"
    title="Error updating data"
    cancelText="Close"
    submitText=""
    size="modal-lg"
    backdrop="true"
    keyboard="true">
</x-modals.parthub-modal>


@include('footer')

{{-- Toasts --}}
{{-- @yield('toasts') --}}
{{-- For some reason the toast needs to be placed before the modals and menus, otherwise it won't show --}}
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="tConfirmDelete" class="toast" role="alert">
        <div class="toast-header">
            <i class="bi bi-check-square-fill text-primary"></i>
            <strong class="me-auto text-primary">&nbsp; PartHub</strong>
            {{-- <small>now</small> --}}
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            <div class="text-success m-0 p-1">
                Successfully deleted <span id="numDeletedItems"></span> <span id="typeSpan"></span>.
            </div>
        </div>
    </div>
</div>

<!-- Hidden template for the editable cell content in category table -->
<div id="treegrid-editable-template-container" class="d-none">
    @include('components.tables.td-editable-flexbox')
</div>

{{-- Modals and Menus --}}
@yield('modals and menus')
</body>

</html>
