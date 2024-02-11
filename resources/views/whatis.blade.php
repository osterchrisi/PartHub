{{-- Parent Template --}}
@extends('centered-layout')

@section('content')
    <table class="table table-borderless text-center mx-auto w-auto" style="borders: false">
        <tr>
            <td colspan="3">
                <h1 class="display-1" id="whatis-headline">What is PartHub?</h1><br>
                <h4>I am an</h2><br>
                <h1>Inventory and BOM management</h1><br>
                I am a tool to help you keep your workplace tidy!
            </td>
        </tr>
        {{-- <tr>
            <td><a href="{{ route('parts') }}">
                    <h1><i class="bi bi-cpu"></i></h1>Parts<br><br>
                </a></td>
            <td><a href="{{ route('boms') }}">
                    <h1><i class="bi bi-clipboard-check"></i></h1>BOMs<br>
                </a></td>
            <td><a href="{{ route('locations') }}">
                    <h1><i class="bi bi-buildings"></i></h1>Storage<br>
                </a></td>
        </tr>
        <tr>
            <td><a href="{{ route('categories') }}">
                    <h1><i class="bi bi-boxes"></i></h1>Categories
                </a></td>
            <td><a href="{{ route('suppliers') }}">
                    <h1><i class="bi bi-cart2"></i></h1>Suppliers
                </a></td>
            <td><a href="{{ route('footprints') }}">
                    <h1><i class="bi bi-outlet"></i></h1>Footprints
                </a></td>
        </tr> --}}
    </table>
@endsection

@section('modals and menus')
    @include('components.modals.userStuffModal')
@endsection

@php
    // Show the login modal if user is not logged in yet
    // if (isset($show_modal) && $show_modal == 1) {
    //     echo '<script>
        //         var myModal = new bootstrap.Modal(document.getElementById('myModal'));
        //         myModal.show();
        //     
    // </script>';
    // }
@endphp
