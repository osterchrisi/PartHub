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
        <div class="row collapse" id="parts-filter-form">
            @yield('filter-form')
        </div>

        <div class='row'>
            {{-- Categories Window - only in Parts view --}}
            @if (isset($view) && $view === 'parts')
                <div><button type="button" class="btn btn-sm btn-outline-secondary"
                        style="--bs-btn-padding-y: .05rem; --bs-btn-padding-x: .25rem; --bs-btn-font-size: .75rem;"
                        id="cat-show-btn">Toggle Categories</button></div>
                <div class='col-md-auto pe-0' id='category-window' style="display: none;">
                    @include('categories.categoriesTable')
                </div>
            @endif

            {{-- Table Window --}}
            <div class='col-9 border rounded border-primary border-opacity-25 mx-3' id='table-window' style='max-width: 90%;'>
                @yield('table-window')
            </div>

            {{-- Info Window --}}
            <div class='col d-flex resizable sticky justify-content-center info-window pb-3 rounded border border-info border-opacity-25' id='info-window'
                style="position: sticky; top: 50px; height: 89vh;">
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

{{-- Modals and Menus --}}
@yield('modals and menus')
</body>

</html>
