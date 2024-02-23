{{-- Parent Template --}}
@extends('app')

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
<h6><br>Click a row in the table to see location details</h6>
@endsection