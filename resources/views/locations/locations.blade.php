{{-- Parent Template --}}
@extends('app')

@section('filter-form')
dis filter
@endsection

@section('table-window')

@include('locations.locationsTable')

@endsection

@section('info-window')
Click a row in the table to see Location details
@endsection