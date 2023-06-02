{{-- Parent Template --}}
@extends('centered-layout')

@section('content')
    <table class="table table-borderless text-center mx-auto w-auto" style="borders: false">
        <tr>
            <td colspan="4">
                <h1 class="display-1" id="welcome-headline">PartHub</h1><br>
                <h1>Pricing</h1><br>

                PartHub helps you focus on the fun part of making things<br>
                by taking care of the annoying tasks of stock keeping for you!
            </td>
        </tr>
        <tr>
            <td><a href="{{ route('parts') }}">
                    <h1><i class="bi bi-1-square"></i></h1>Enthusiast<br><br>
                </a></td>
            <td><a href="{{ route('boms') }}">
                    <h1><i class="bi bi-2-square"></i></h1>Maker<br>
                </a></td>
            <td><a href="{{ route('locations') }}">
                    <h1><i class="bi bi-3-square"></i></h1>Serious Playah<br>
                </a></td>
            <td><a href="{{ route('locations') }}">
                    <h1><i class="bi bi-4-square"></i></h1>Big Corp<br>
                </a></td>
        </tr>
    </table>
@endsection

@section('modals and menus')
@endsection
