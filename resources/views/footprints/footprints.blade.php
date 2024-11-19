{{-- Parent Template --}}
@extends('app')

{{-- Toolbar Buttons --}}
@php
$showAddButton = true;
$showDeleteButton = true;
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
<x-info-window-info-div>
    Click a row in the table to see footprint details
</x-info-window-info-div>
@endsection