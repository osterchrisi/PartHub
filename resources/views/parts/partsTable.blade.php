<?php
use App\Models\Part;
use App\Http\Controllers\PartsController;
?>

<div class='col-9' id='table-window' style='max-width: 90%;'>
    <?php
    // Build Parts Table
    $pc = new PartsController();
    $parts = Part::queryParts($search_column, $search_term, $column_names, $search_category, $user_id);
    $pc->buildTable($parts);
    ?>

    this is where the table goes
</div>
