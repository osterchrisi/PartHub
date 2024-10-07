{{-- Parent Template --}}
@extends('app')

{{-- Toolbar Buttons --}}
@php
$showAddButton = true;
$showDeleteButton = true;
$showEditButton = true;
$showFilterButton = true;
@endphp

{{-- Modals and Menus --}}
@section('modals and menus')
    @include('components.modals.supplierEntryModal', ['supplier_name' => ''])
@endsection

@section('filter-form')
Filter comming soon
@endsection

@section('table-window')

@include('suppliers.suppliersTable')

@endsection

@section('info-window')
<x-info-window-info-div>
    Click a row in the table to see supplier details
</x-info-window-info-div>
@endsection