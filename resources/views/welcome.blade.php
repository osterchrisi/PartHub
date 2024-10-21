{{-- Parent Template --}}
@extends('centered-layout')

@section('content')
    <table class="table table-borderless text-center mx-auto w-auto" style="borders: false">
        <tr>
            <td colspan="3">
                <h1 class="display-1">PartHub</h1><br>
                <h1>Inventory and BOM management</h1><br>
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
                    <a href="{{ route('parts') }}" class="list-group-item list-group-item-action text-center bg-transparent welcome-items">
                        <h1><i class="bi bi-cpu"></i></h1>Parts
                    </a>
                </div>
            </td>
            <td width="33.3%">
                <div class="list-group">
                    <a href="{{ route('boms') }}" class="list-group-item list-group-item-action text-center bg-transparent welcome-items">
                        <h1><i class="bi bi-clipboard-check"></i></h1>BOMs
                    </a>
                </div>
            </td>
            <td width="33.3%">
                <div class="list-group">
                    <a href="{{ route('locations') }}" class="list-group-item list-group-item-action text-center bg-transparent welcome-items">
                        <h1><i class="bi bi-buildings"></i></h1>Storage
                    </a>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <table class="table table-borderless">
                    <tr>
                        <td width="11.33%"></td>
                        <td width="33%">
                            <div class="list-group">
                                <a href="{{ route('suppliers') }}" class="list-group-item list-group-item-action text-center bg-transparent welcome-items">
                                    <h1><i class="bi bi-cart2"></i></h1>Suppliers
                                </a>
                            </div>
                        </td>
                        <td width="11.33%"></td>
                        <td width="33%">
                            <div class="list-group">
                                <a href="{{ route('footprints') }}" class="list-group-item list-group-item-action text-center bg-transparent welcome-items">
                                    <h1><i class="bi bi-outlet"></i></h1>Footprints
                                </a>
                            </div>
                        </td>
                        <td width="11.33%"></td>
                    </tr>
                </table>
            </td>
        </tr>
        
        
    </table>
@endsection

@section('modals and menus')
@endsection
