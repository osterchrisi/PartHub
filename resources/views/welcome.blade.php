{{-- Parent Template --}}
@extends('centered-layout')

@section('content')
    <table class="table table-borderless text-center mx-auto w-auto" style="borders: false">
        <tr>
            <td colspan="3">
                {{-- <h1 class="display-1 text-white">PartHub</h1><br> --}}
                <img src="logos/Svg file - white text.svg" alt="PartHub" width="300">
                <br><br>
                <h1 class="text-white">Inventory and BOM management</h1><br>
                @if (session('loggedIn'))
                    <div class="alert alert-info" role="alert">Nice to have you back, {{ optional(Auth::user())->name }}! 🚀
                    </div>
                @elseif (session('firstLogin'))
                    <div class="alert alert-info" role="alert">Glad to have you onboard,
                        {{ optional(Auth::user())->name }}!<br>Your registration was successful! Please check your email for
                        further instructions.
                        🚀</div>
                @endif
            </td>
        </tr>
        <tr>
            <td width="33.3%">
                <div class="list-group">
                    <a href="{{ route('parts') }}"
                        class="list-group-item list-group-item-action text-center bg-transparent welcome-items text-white">
                        <h1><i class="bi bi-cpu"></i></h1>Parts
                    </a>
                </div>
            </td>
            <td width="33.3%">
                <div class="list-group">
                    <a href="{{ route('boms') }}"
                        class="list-group-item list-group-item-action text-center bg-transparent welcome-items text-white">
                        <h1><i class="bi bi-clipboard2-check"></i></h1>BOMs
                    </a>
                </div>
            </td>
            <td width="33.3%">
                <div class="list-group">
                    <a href="{{ route('locations') }}"
                        class="list-group-item list-group-item-action text-center bg-transparent welcome-items text-white">
                        <h1><i class="bi bi-box-seam"></i></h1>Storage
                    </a>
                </div>
            </td>
        </tr>

        <tr>
            <td width="33.3%">
                <div class="list-group">
                    <a href="{{ route('suppliers') }}"
                        class="list-group-item list-group-item-action text-center bg-transparent welcome-items text-white">
                        <h1><i class="bi bi-truck"></i></h1>Suppliers
                    </a>
                </div>
            </td>
            <td width="33.3%">
                <div class="list-group">
                    <a href="{{ route('footprints') }}"
                        class="list-group-item list-group-item-action text-center bg-transparent welcome-items text-white">
                        <h1><i class="bi bi-bug"></i></h1>Footprints
                    </a>
                </div>
            </td>
            <td width="33.3%">
                <div class="list-group">
                    <a href="{{ route('manufacturers') }}"
                        class="list-group-item list-group-item-action text-center bg-transparent welcome-items text-white">
                        <h1><i class="bi bi-gear-wide-connected"></i></h1>Manufacturers
                    </a>
                </div>
            </td>
        </tr>


    </table>
@endsection

@section('modals and menus')
@endsection
