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
        <button type="button" class="btn btn-sm btn-primary btn-labeled" id="toolbarAssembleBomButton"><span class="btn-label">
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
    <div class="alert alert-dark align-self-start mt-3" role="alert">
        <p class="text-center">
        <h6>Click a row in the table to see BOM details</h6>
        </p>
        <br>
        @if (session('success'))
            <div class="alert alert-success align-self-start mt-3">
                {{ session('success') }}
            </div>
        @endif
    </div>
@endsection
