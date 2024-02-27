{{-- Parent Template --}}
@extends('app')

{{-- Modals and Menus --}}
@section('modals and menus')
    @include('components.modals.supplierEntryModal', ['supplier_name' => ''])
@endsection

@section('filter-form')
dis filter
@endsection

@section('table-window')

@include('suppliers.suppliersTable')

@endsection

@section('info-window')
<h6><br>Click a row in the table to see supplier details</h6>
@endsection