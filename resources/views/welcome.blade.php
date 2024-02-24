{{-- Parent Template --}}
@extends('centered-layout')

@section('content')
    <table class="table table-borderless text-center mx-auto w-auto" style="borders: false">
        <tr>
            <td colspan="3">
                <h1 class="display-1" id="welcome-headline">PartHub</h1><br>
                <h1>Inventory and BOM management</h1><br>
                @if (session('loggedIn'))
                    <div class="alert alert-info" role="alert">Nice to have you back,
                        {{ optional(Auth::user())->name }}!</div>
                @endif

                @unless (auth()->check())
                    Hello internet stranger that has found their way to PartHub! 👋 <br>
                    PartHub is currently in beta and you can use it and snatch a <a href="{{ route('signup') }}">free early-bird
                        account</a>.<br><br>

                    If you just want to hang around and explore the app, click some stuff, that is fine with us too!<br>
                    Go ahead and explore by logging in as the demo user below!<br>
                    <br>As a heads up, the demo database will reset every once in a while<br><br>
                @endunless
            </td>
        </tr>
        <tr>
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
        </tr>
        @unless (auth()->check())
            <tr>
                <td colspan="3">
                    <table class="table table-borderless">
                        <tbody class="alert alert-danger">
                            <tr>
                                <td>
                                    <form id="demoLoginButton" action="{{ route('demo.login') }}" method="GET">
                                        @csrf<button type="submit" class="btn btn-primary" id="continueDemo">Continue as demo
                                            user</button></form>
                                </td>
                                <td>
                                    <form id="login-button" action="{{ route('login') }}" method="GET">
                                        @csrf<button type="submit" class="btn btn-primary" id="logIn">Log into your
                                            account</button></form>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        @endunless
    </table>
    <!-- Google tag (gtag.js) event -->
    <script>
        gtag('event', 'manual_event_PAGE_VIEW', {
            // <event_parameters>
        });
    </script>
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
        // 
    </script>';
    // }
@endphp
