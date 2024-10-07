{{-- Parent Template --}}
@extends('app')

{{-- Modals and Menus --}}
@section('modals and menus')
    @include('components.modals.bomAssemblyModal')
    @include('components.menus.bomsTableRightClickMenu')
@endsection

{{-- Toolbar Buttons --}}
@php
$showAddButton = true;
$showDeleteButton = true;
$showEditButton = true;
$showFilterButton = true;
$showAssembleButton = true;
@endphp

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
<x-info-window-info-div>
    Click a row in the table to see BOM details
</x-info-window-info-div>
@endsection
