{{-- Header --}}
@include('header')

{{-- Navbar and Toolbar --}}
@include('navbar')
@include('components.toolbarTop')

{{-- Page Contents --}}
<div class="container-fluid" id="content_container">
    <br>
    <div class="row" id="content_row">
        @yield('filter_form')
        <div class='row'>
            @yield('table-window')
            @yield('info-window')
        </div>
    </div>
</div>

{{-- Modals and Menus --}}
@yield('modals_n_menus')

{{-- Pretty hacky way of doing this but for porting to Laravel and making it work, I let it be --}}
<script src="http://127.0.0.1:5173/resources/js/partEntry.js"></script>
<script src="http://127.0.0.1:5173/resources/js/tables.js"></script>
<script src="http://127.0.0.1:5173/resources/js/custom.js"></script>
@vite(['resources/js/tables.js', 'resources/js/partsView.js', 'resources/js/partEntry.js'])
