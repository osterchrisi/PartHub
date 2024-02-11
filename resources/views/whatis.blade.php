{{-- Parent Template --}}
@extends('centered-layout')

@section('content')
    <table class="table table-borderless text-center mx-auto w-auto" style="borders: false">
        <tr>
            <td colspan="3">
                <h1 class="display-1" id="whatis-headline">What is PartHub?</h1><br>
                <h4>TLDR; I am an</h2><h1>Inventory and BOM management software</h1><br>
                <p>...I am here to help you keep your workplace tidy</h5><br>
                    I take care of the boring stock keeping and you can focus on the creative part of your work.</p>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <h1 class="display-1" id="whatis-headline">How can PartHub help me?</h1><br>
                {{-- <h4>TLDR; I am an</h2><h1>Inventory and BOM management software</h1><br> --}}
                <p>I make tracking stock fun. It's like a zombie shooter video game.<br>
                    Only that you use a barcode scanner as a gun and shoot labels instead of zombies.<br>
                (Also works without a barcode scanner)</p>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <h1 class="display-1" id="whatis-headline">Is PartHub for me?</h1><br>
                {{-- <h4>TLDR; I am an</h2><h1>Inventory and BOM management software</h1><br> --}}
                <p>Yes! If you buy electronic parts for your projects and they come in small baggies with labels,<br>
                    I am here to make your life significantly easier!</p>
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
