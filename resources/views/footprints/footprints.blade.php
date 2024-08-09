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
    @include('components.modals.footprintEntryModal', ['footprint_name' => ''])
@endsection

@section('filter-form')
dis filter
@endsection

@section('table-window')

@include('footprints.footprintsTable')

@endsection

@section('info-window')
<h6><br>Click a row in the table to see footprint details</h6>
@endsection