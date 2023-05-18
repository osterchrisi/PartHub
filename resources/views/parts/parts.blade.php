<?php
use App\Models\User;
use App\Models\Part;
use App\Models\Location;
use App\Models\Category;
use App\Http\Controllers\PartsController;

//! This is double (once in header already), fix this
$user = optional(auth()->user());
$user_id = $user ? $user->id : 0;
$user_name = $user ? $user->name : '';

// Parts stuff
$column_names = Part::getColumnNames();
$locations = Location::availableLocations($user_id);
$categories = Category::availableCategories($user_id);

// For categories dropdown
$search_category = isset($_GET['cat']) ? $_GET['cat'] : ['all'];
$sc = PartsController::extractCategoryIds($search_category);
?>

{{-- Parent Template --}}
@extends('app')

{{-- Parts filter form --}}
@section('filter_form')
    <div class="row collapse" id="parts-filter-form">
        <div class="col-3" id="search-box-div">
            <form method="get" id="search_form" action=" {{ route('parts') }}">
                <input type="text" class="form-control form-control-sm" id="search" name="search"
                    placeholder="Start typing to filter..." value="{{ $search_term }}"><br><br><br>
        </div>
        <div class="col-3" id="category-box-div">
            <input type="hidden" name="cat[]" id="selected-categories" value="">
            @include('components.selects.categoryMultiSelect')
        </div>
        <div class="col-1" id="search-button-div">
            <button type="submit" class="btn btn-sm btn-primary">Search</button><br><br>
        </div>
        </form>
    </div>
@endsection

{{-- Table Window --}}
@section('table-window')
    <div class='col-9' id='table-window' style='max-width: 90%;'>
        @include('parts.partsTable')
    </div>
@endsection

{{-- Info Window --}}
@section('info-window')
    <div class='col d-flex resizable sticky justify-content-center info-window pb-3' id='info-window'
        style="position: sticky; top: 50px; height: 89vh;">
        <h6><br>Click on a row in the table</h6>
    </div>
@endsection

{{-- Modals and Menus --}}
@section('modals_n_menus')
    @include('components.modals.stockModal')
    @include('components.modals.partEntryModal', ['part_name' => ''])
    @include('components.menus.partsTableRightClickMenu')
@endsection
