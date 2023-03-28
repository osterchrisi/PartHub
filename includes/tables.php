<?php
function buildTable($column_names, $nice_columns, $result, $width = "100%")
{
    echo '<div style="overflow-x:auto;">';
    echo '<table class="table table-striped table-hover table-sm" style="width: ' . $width . '; font-size:12px">';

    // Table headers
    echo "<thead>";
    echo "<tr>";
    foreach ($nice_columns as $column_header) {
        echo "<th>$column_header</th>";
    }
    echo "</tr>";
    echo "</thead>";

    // Table rows
    foreach ($result as $row) {
        echo "<tr>";
        foreach ($column_names as $column_data) {
            echo "<td>" . $row[$column_data] . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
}

function buildBomTable($bom_list, $db_columns, $nice_columns, $width = "100%")
{
    echo '<div class="table-responsive" style="overflow-x:auto; font-size:12px">';
    echo '<table
            class="table table-hover table-striped table-sm"
            id="BomListTable"
            data-resizable="true"
            data-search="true"
            data-search-align="left"
            data-show-columns="true"
            data-reorderable-columns="true"
            data-cookie="true"
            data-cookie-id-table="rememberTableState"
            >';

    // Table headers
    echo "<thead class='table table-sm table-dark'>";
    echo "<tr>";
    foreach ($nice_columns as $column_header) {
            echo "<th data-field='$column_header'>$column_header</th>";
    }
    echo "</tr>";
    echo "</thead>";

    echo "<tbody>";
    // Table rows
    foreach ($bom_list as $row) {
        // echo "<tr>";
        $bom_id = $row['bom_id'];
        echo "<tr data-id=" . $row['bom_id'] . ">";
        foreach ($db_columns as $column_data) {
                echo "<td data-editable='true' class='editable' data-id=" . $bom_id . " data-column=" . $column_data . ">" . $row[$column_data] . "</td>";
        }
        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";
    echo "</div>";
}

function buildPartsTable($result, $db_columns, $nice_columns, $total_stock, $conn, $table_name, $width = "100%")
{
    echo '<div class="table-responsive" style="overflow-x:auto; font-size:12px">';
    echo '<table
            class="table table-hover table-striped table-sm"
            id="parts_table"
            data-resizable="true"
            data-search="true"
            data-search-selector="#filter"
            data-search-align="left"
            data-show-columns="true"
            data-reorderable-columns="true"
            data-cookie="true"
            data-cookie-id-table="rememberTableState"
            >';

    // Table headers
    echo "<thead class='table table-sm table-dark'>";
    echo "<tr>";
    foreach ($nice_columns as $column_header) {
        if ($column_header == 'Total Stock') {
            echo "<th data-sortable='true' data-sorter='NumberURLSorter' data-field='$column_header'>$column_header</th>";
        }
        else {
            echo "<th data-field='$column_header'>$column_header</th>";
        }
    }
    echo "</tr>";
    echo "</thead>";

    echo "<tbody>";
    // Table rows
    foreach ($result as $row) {
        // echo "<tr>";
        $part_id = $row['part_id'];
        echo "<tr data-id=" . $row['part_id'] . ">";
        foreach ($db_columns as $column_data) {
            if ($column_data == 'total_stock') {
                // Get total stock
                $stock = getStockLevels($conn, $part_id);
                $total_stock = getTotalStock($stock);
                // Display total stock number as link to showing stock levels
                echo "<td style='text-align:right'><a href='show-stock.php?part_id=$part_id'>" . $total_stock . "</a></td>";
            }
            elseif ($column_data == 'category_name') {
                echo "<td data-editable='true' class='editable category' data-id=" . $part_id . " data-column=" . $column_data . " data-table_name=" . $table_name . ">" . $row[$column_data] . "</td>";
            }
            else { // Any other table data available
                echo "<td data-editable='true' class='editable' data-id=" . $part_id . " data-column=" . $column_data . " data-table_name=" . $table_name . ">" . $row[$column_data] . "</td>";
            }
        }

        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";
    echo "</div>";
}

function buildPartStockHistoryTable($db_columns, $nice_columns, $stock_history, $width = "100%")
{
    echo '<div class="table-responsive" style="overflow-x:auto; font-size:12px">';
    echo '<table
            class="table table-hover table-striped table-sm"
            id="partStockHistoryTable"
            data-resizable="true"
            data-search="true"
            data-search-align="left"
            data-show-columns="true"
            data-reorderable-columns="true"
            data-cookie="true"
            data-cookie-id-table="rememberTableState"
            >';

    // Table headers
    echo "<thead class='table table-sm table-dark'>";
    echo "<tr>";
    foreach ($nice_columns as $column_header) {
        if ($column_header == 'Date') {
            echo "<th data-sortable='true' data-field='$column_header'>$column_header</th>";
        }
        else {
            echo "<th data-field='$column_header'>$column_header</th>";
        }
    }
    echo "</tr>";
    echo "</thead>";

    echo "<tbody>";
    // Table rows
    foreach ($stock_history as $row) {
        // echo "<tr>";
        $hist_id = $row['stock_lvl_chng_id'];
        echo "<tr data-id=" . $row['part_id'] . ">";
        foreach ($db_columns as $column_data) {
            if ($column_data == 'stock_lvl_chng_comment') {
                echo "<td data-editable='true' class='editable' data-id=" . $hist_id . " data-column=" . $column_data . ">" . $row[$column_data] . "</td>";
            }
            else { // Any other table data available
                echo "<td data-editable='true' class='editable' data-id=" . $hist_id . " data-column=" . $column_data . ">" . $row[$column_data] . "</td>";
            }
        }

        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";
    echo "</div>";
}

//! When I wrote this function I wasn't yet clear about what to use as the data-id. But only relevant, once this table should become editable
function buildPartInBomsTable($db_columns, $nice_columns, $bom_list, $width = "100%")
{
    echo '<div class="table-responsive" style="overflow-x:auto; font-size:12px">';
    echo '<table
            class="table table-hover table-striped table-sm"
            id="partInBomsTable"
            data-resizable="true"
            data-search="true"
            data-search-align="left"
            data-show-columns="true"
            data-reorderable-columns="true"
            data-cookie="true"
            data-cookie-id-table="rememberTableState"
            >';

    // Table headers
    echo "<thead class='table table-sm table-dark'>";
    echo "<tr>";
    foreach ($nice_columns as $column_header) {
        echo "<th data-field='$column_header'>$column_header</th>";
    }
    echo "</tr>";
    echo "</thead>";

    echo "<tbody>";
    // Table rows
    foreach ($bom_list as $row) {
        // echo "<tr>";
        $bom_id = $row['bom_id'];
        echo "<tr data-id=" . $row['bom_elements_id'] . ">";
        foreach ($db_columns as $column_data) {
            echo "<td data-editable='true' data-id=" . $bom_id . " data-column=" . $column_data . ">" . $row[$column_data] . "</td>";
        }

        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";
    echo "</div>";
}