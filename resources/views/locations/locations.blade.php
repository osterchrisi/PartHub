{{-- Parent Template --}}
@extends('app')

{{-- Toolbar Buttons --}}
@php
$showAddButton = true;
$showDeleteButton = true;
@endphp

{{-- Modals and Menus --}}
@section('modals and menus')
    @include('components.modals.locationEntryModal', ['location_name' => ''])
@endsection

@section('filter-form')
Filter locations
@endsection

@section('table-window')

@include('locations.locationsTable')

@endsection

@section('info-window')
<x-info-window-info-div>
    Click a row in the table to see location details
</x-info-window-info-div>
@endsection