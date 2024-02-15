{{-- Parent Template --}}
@extends('app')

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
dis Info
@endsection