{{-- Parent Template --}}
@extends('app')

{{-- Toolbar Buttons --}}
@php
$showAddButton = true;
$showDeleteButton = true;
$showEditButton = true;
$showFilterButton = true;
@endphp

{{-- Need to figure out why the order of these modals matters.
If the right-click menu is not on top, it won't show --}}
{{-- Modals and Menus --}}
@section('modals and menus')
    @include('components.menus.partsTableRightClickMenu')
    @include('components.modals.stockModal')
    @include('components.modals.categoryEntryModal')
    @include('components.modals.categoryCreationModal')
    @include('components.modals.partEntryModal', ['part_name' => ''])
@endsection

{{-- Filter form --}}
@section('filter-form')
    <div class="col-3" id="search-box-div">
        <form method="get" id="search_form" action=" {{ route('parts') }}">
            <input type="text" class="form-control form-control-sm" id="search" name="search"
                placeholder="Start typing to filter..." value="{{ $search_term }}"><br><br><br>
    </div>
    <div class="col-3" id="category-box-div">
        <input type="hidden" name="cat[]" id="selected-categories" value="">
        @include('components.selects.categoryMultiSelect')
    </div>
    <div class="col-1" id="search-button-div">
        <button type="submit" class="btn btn-sm btn-primary">Search</button><br><br>
    </div>
    </form>
@endsection

{{-- Table Window --}}
@section('table-window')
    @include('parts.partsTable')
@endsection

{{-- Info Window --}}
@section('info-window')
    {{-- <div class="alert alert-dark align-self-start mt-3 mx-3" role="alert">
        <h6 class="text-center">Click a row in the table to see part details</h6>
    </div> --}}
    <x-info-window-info-div>
        Click a row in the table to see part details
    </x-info-window-info-div>
@endsection
