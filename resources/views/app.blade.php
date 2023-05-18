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
{{-- Header --}}
@include('header')

{{-- Visible Page Contents --}}
@include('navbar')
@include('components.toolbarTop')

<div class="container-fluid">
    <br>
    <div class="row">

        {{-- Parts filter form --}}

        <!-- Table div and info div -->
        <div class='row'>
            <div class='col-9' id='table-window' style='max-width: 90%;'>
                @include('parts.partsTable')
            </div>
            <div class='col d-flex resizable sticky justify-content-center info-window pb-3' id='info-window'
                style="position: sticky; top: 50px; ">
                <h6><br>Click on a row in the table</h6>
            </div>
        </div>
    </div>
</div>
{{-- Pretty hacky way of doing this but for porting to Laravel and making it work, I let it be --}}
<script src="http://127.0.0.1:5173/resources/js/partEntry.js"></script>
<script src="http://127.0.0.1:5173/resources/js/tables.js"></script>
<script src="http://127.0.0.1:5173/resources/js/custom.js"></script>
@vite(['resources/js/tables.js', 'resources/js/partsView.js', 'resources/js/partEntry.js'])
