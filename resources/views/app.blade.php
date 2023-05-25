{{-- Header --}}
@include('header')

{{-- Navbar and Toolbar --}}
@include('navbar')
@include('components.toolbarTop')

{{-- Page Contents --}}
<div class="container-fluid" id="content-container">
    <br>
    <div class="row" id="content-row">
        {{-- Filter Form --}}
        <div class="row collapse" id="parts-filter-form">
            @yield('filter-form')
        </div>

        <div class='row'>
            {{-- Table Window --}}
            <div class='col-9' id='table-window' style='max-width: 90%;'>
                @yield('table-window')
            </div>

            {{-- Info Window --}}
            <div class='col d-flex resizable sticky justify-content-center info-window pb-3' id='info-window'
                style="position: sticky; top: 50px; height: 89vh;">
                @yield('info-window')
            </div>
        </div>

    </div>
</div>

{{-- Modals and Menus --}}
@yield('modals and menus')

{{-- Toasts --}}
{{-- @yield('toasts') --}}
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="tConfirmDelete" class="toast" role="alert">
        <div class="toast-header">
            {{-- <img src="..." class="rounded me-2" alt="..."> --}}
            <strong class="me-auto">PartHub</strong>
            <small>now</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            Successfully deleted <span id="numDeletedItems"></span> row(s).
        </div>
    </div>
</div>

{{-- Pretty hacky way of doing this but for porting to Laravel and making it work, I let it be --}}
<script src="http://127.0.0.1:5173/resources/js/partEntry.js"></script>
<script src="http://127.0.0.1:5173/resources/js/tables.js"></script>
<script src="http://127.0.0.1:5173/resources/js/custom.js"></script>
<script src="http://127.0.0.1:5173/resources/js/stockChanges.js"></script>
