{{-- Parent Template --}}
@extends('app')

{{-- Modals and Menus --}}
@section('modals_n_menus')
    @include('components.modals.stockModal')
    @include('components.modals.partEntryModal', ['part_name' => ''])
    @include('components.menus.partsTableRightClickMenu')
@endsection

{{-- Extra Toolbar Buttons --}}
@section('page-specific-buttons')
    <li class="nav-item p-1">
        <button type="button" class="btn btn-sm btn-primary btn-labeled" data-bs-toggle="collapse"
            data-bs-target="#parts-filter-form"><span class="btn-label"><i
                    class="fas fa-lg fa-wrench"></i></span>Assemble</button>
    </li>
@endsection
