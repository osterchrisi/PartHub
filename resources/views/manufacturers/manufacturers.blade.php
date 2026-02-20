{{-- Parent Template --}}
@extends('app')

{{-- Toolbar Buttons --}}
@php
$showAddButton = true;
$showDeleteButton = true;
@endphp

{{-- Modals and Menus --}}
@section('modals and menus')
    @include('components.modals.manufacturerEntryModal', ['manufacturer_name' => ''])
@endsection

@section('filter-form')
Filter manufacturers
@endsection

@section('table-window')
    @include('manufacturers.manufacturersTable')
@endsection

@section('info-window')
    <x-info-window-info-div>
        Click a row in the table to see manufacturer details
    </x-info-window-info-div>
@endsection
