{{-- Parent Template --}}
@extends('app')

{{-- Modals and Menus --}}
@section('modals and menus')
    @include('components.modals.bomAssemblyModal')
    @include('components.menus.bomsTableRightClickMenu')
@endsection

{{-- Extra Toolbar Buttons --}}
@section('page specific buttons')
    <li class="nav-item p-1">
        <button type="button" class="btn btn-sm btn-primary btn-labeled"><span class="btn-label">
                <i class="fas fa-lg fa-wrench"></i></span>Assemble</button>
    </li>
@endsection

{{-- Filter Form --}}
@section('filter-form')
    This is filter
@endsection

{{-- Table Window --}}
@section('table-window')
    @include('boms.bomsTable')
@endsection

{{-- Info Window --}}
@section('info-window')
    This is info window
@endsection
