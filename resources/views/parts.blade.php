<?php
use App\Models\User;
use App\Models\Part;

// User stuff
$user = optional(auth()->user());
$user_id = $user ? $user->id : 0;
$user_name = $user ? $user->name : '';

// Parts stuff
$search_column = 'everywhere';
$search_term = '';
$column_names = Part::getColumnNames();
$search_category = ['all'];

?>

{{-- Visible Page Contents --}}
@include('header')
@include('navbar')
@include('components.toolbarTop')

{{-- Modals and Menus --}}
@include('components.modals.stockModal')
@include('components.modals.partEntryModal', ['part_name' => ''])
@include('components.menus.partsTableRightClickMenu')

<div class="container-fluid">
    <br>
    <div class="row">

        <!-- Parts filter form -->
        <div class="row collapse" id="parts-filter-form">
            <div class="col-3" id="search-box-div">
                <form method="get" id="search_form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <input type="text" class="form-control form-control-sm" id="search" name="search"
                        placeholder="Filter parts..." value="<?php echo htmlspecialchars($search_term); ?>"><br><br><br>
            </div>
            <div class="col-3" id="category-box-div">
                <input type="hidden" name="cat[]" id="selected-categories" value="">
                <?php
                // generateCategoriesDropdown($categories, $search_category);
                ?>
            </div>
            <div class="col-1" id="search-button-div">
                <button type="submit" class="btn btn-sm btn-primary">Search</button><br><br>
            </div>
            </form>
        </div>

        <!-- Table div and info div -->
        <div class='row'>
            <div class='col-9' id='table-window' style='max-width: 90%;'>
                <?php
                // Build Parts Table
                echo var_dump($parts);
                ?>
            </div>
            <div class='col d-flex h-50 resizable sticky-top justify-content-center info-window pb-3' id='info-window'>
                <h6><br>Click on a row in the table</h6>
            </div>
        </div>
    </div>
</div>
