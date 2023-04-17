<?php
// Tables get built here
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

// This is silly but I want inline stock view so hard, I'll leave it like this for now
// This is essentially a copy of the buildTable function but returns a string that I can pass
// into the Popover div
function buildHTMLTable($column_names, $nice_columns, $result, $width = "100%")
{
    $html = "<div style='overflow-x:auto;'>";
    $html .= "<table class='table table-striped table-hover table-sm' style='width: " . $width . "; font-size:12px'>";

    // Table headers
    $html .= "<thead>";
    $html .= "<tr>";
    foreach ($nice_columns as $column_header) {
        $html .= "<th>$column_header</th>";
    }
    $html .= "</tr>";
    $html .= "</thead>";

    // Table rows
    foreach ($result as $row) {
        $html .= "<tr>";
        foreach ($column_names as $column_data) {
            $html .= "<td>" . $row[$column_data] . "</td>";
        }
        $html .= "</tr>";
    }
    $html .= "</table>";
    $html .= "</div>";
    return $html;
}

function buildBomListTable($bom_list, $db_columns, $nice_columns, $table_name, $id_field, $width = "100%")
{
    echo '<div class="table-responsive" style="overflow-x:auto; font-size:12px">';
    echo '<table
            class="table table-hover table-striped table-sm"
            id="bom_list_table"
            data-resizable="true"
            data-search="true"
            data-search-align="left"
            data-show-columns="true"
            data-reorderable-columns="true"
            data-cookie="true"
            data-cookie-id-table="BomListTableState"
            data-max-moving-rows="100"
            data-multiple-select-row="true"
            data-click-to-select="true"
            >';

    // Table headers
    echo "<thead class='table table-sm table-dark'>";
    echo "<tr>";

    // This column is for Bootstrap Table Click-To-Select to work
    echo '<th data-field="state" data-checkbox="true"></th>';

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
            if ($column_data == 'state') {
                ;
            }
            else {
                echo "<td data-editable='true' class='editable' data-id=" . $bom_id . " data-column=" . $column_data . " data-table_name=" . $table_name . " data-id_field=" . $id_field . ">" . $row[$column_data] . "</td>";
            }
        }
        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";
    echo "</div>";
}

function buildBomDetailsTable($db_columns, $nice_columns, $bom_elements, $conn, $width = "100%")
{
    echo '<div class="table-responsive" style="overflow-x:auto; font-size:12px">';
    echo '<table
            class="table table-hover table-striped table-sm"
            id="BomDetailsTable"
            data-resizable="true"
            data-search="true"
            data-search-align="left"
            data-show-columns="true"
            data-reorderable-columns="true"
            data-cookie="true"
            data-cookie-id-table="BomDetailsTableState"
            data-max-moving-rows="100"
            >';

    // Table headers
    echo "<thead class='table table-sm table-dark'>";
    echo "<tr>";
    foreach ($nice_columns as $column_header) {
        if ($column_header == 'Quantity needed' || $column_header == 'Total stock available') {
            // Align quantity headers right
            echo "<th data-halign='right' data-field='$column_header'>$column_header</th>";
        }
        elseif ($column_header == 'Can build') {
            // Align the "Can build" column header right and make this column sortable
            echo "<th data-halign='right' data-field='$column_header' data-sortable='true'>$column_header</th>";
        }
        else {
            // Any other column header
            echo "<th data-field='$column_header'>$column_header</th>";
        }
    }
    echo "</tr>";
    echo "</thead>";

    echo "<tbody>";
    // Table rows
    foreach ($bom_elements as $row) {
        // echo "<tr>";

        $part_id = $row['part_id'];
        $part_name = $row['part_name'];

        // This is the popover mini stock table
        $db_columns_inline = array('location_name', 'stock_level_quantity');
        $nice_columns_inline = array('Location', 'Quantity');
        $result = getStockLevels($conn, $part_id);
        $inline_table_content = buildHTMLTable($db_columns_inline, $nice_columns_inline, $result);

        // The "actual" BOM details table
        echo "<tr data-id=" . $row['part_id'] . ">";
        foreach ($db_columns as $column_data) {
            if ($column_data == 'stock_available') {
                // Get total stock
                $stock = getStockLevels($conn, $part_id);
                $total_stock = getTotalStock($stock);
                // Display total stock number as link to showing stock levels in popover table
                echo '<td style="text-align:right"><a tabindex="0" role="button" data-bs-trigger="focus" data-bs-toggle="popover" data-bs-title="Stock for ' . $part_name . '" data-bs-html="true" data-bs-content="' . $inline_table_content . '" data-bs-sanitize="false" href="#">' . $total_stock . '</a></td>';
            }
            elseif ($column_data == 'element_quantity') {
                // Align quantity cells right
                echo "<td style='text-align:right' data-id=" . $part_id . " data-column=" . $column_data . ">" . $row[$column_data] . "</td>";
            }
            elseif ($column_data == 'can_build') {
                // Align quantity cells right
                $can_build = floor($total_stock / $row['element_quantity']);
                echo "<td style='text-align:right' data-id=" . $part_id . " data-column=" . $column_data . ">$can_build</td>";
            }
            else {
                echo "<td data-id=" . $part_id . " data-column=" . $column_data . ">" . $row[$column_data] . "</td>";
            }
        }
        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";
    echo "</div>";
}

function buildPartsTable($result, $db_columns, $nice_columns, $total_stock, $conn, $table_name, $id_field, $width = "100%")
{
    echo '<div class="table-responsive" style="overflow-x:auto; font-size:12px">';
    echo '<table
            class="table table-hover table-striped table-sm"
            id="parts_table"
            data-resizable="true"
            data-search="true"
            data-search-time-out=""
            data-search-selector="#filter"
            data-search-align="left"
            data-show-columns="true"
            data-reorderable-columns="true"
            data-cookie="true"
            data-cookie-id-table="PartsTableState"
            data-max-moving-rows="100"
            data-multiple-select-row="true"
            data-click-to-select="true"
            >';

    // Table headers
    echo "<thead class='table table-sm table-dark'>";
    echo "<tr>";
    // This column is for Bootstrap Table Click-To-Select to work
    echo '<th data-field="state" data-checkbox="true"></th>';
    foreach ($nice_columns as $column_header) {
        if ($column_header == 'Total Stock') {
            // Removing the in-table links to stock levels again...
            // echo "<th data-sortable='true' data-sorter='NumberURLSorter' data-field='$column_header'>$column_header</th>";
            echo "<th data-sortable='true' data-field='$column_header'>$column_header</th>";
        }
        else {
            echo "<th data-sortable='true' data-field='$column_header'>$column_header</th>";
        }
    }
    echo '<th data-field="state" data-checkbox="true"></th>';
    echo "</tr>";
    echo "</thead>";

    echo "<tbody>";
    // Table rows
    foreach ($result as $row) {
        // echo "<tr>";
        $part_id = $row['part_id'];
        echo "<tr data-id=" . $row['part_id'] . " class='whatever'>";
        foreach ($db_columns as $column_data) {
            // Removing the in-table links to stock levels again...
            if ($column_data == 'total_stock') {
                // Get total stock
                $stock = getStockLevels($conn, $part_id);
                $total_stock = getTotalStock($stock);
                // Display total stock number as link to showing stock levels
                // echo "<td style='text-align:right' data-id=" . $part_id . " data-column=" . $column_data . " data-table_name=" . $table_name . "><a href='show-stock.php?part_id=$part_id'>" . $total_stock . "</a></td>";
                echo "<td style='text-align:right' data-id=" . $part_id . " data-column=" . $column_data . " data-table_name=" . $table_name . " data-id_field=" . $id_field . ">" . $total_stock . "</td>";
            }
            elseif ($column_data == 'category_name') {
                echo "<td data-editable='true' class='editable category' data-id=" . $part_id . " data-column=" . $column_data . " data-table_name=" . $table_name . " data-id_field=" . $id_field . ">" . $row[$column_data] . "</td>";
            }
            elseif ($column_data == 'state') {
                ;
            }
            elseif ($column_data == 'state'){
                ;
            }
            else { // Any other table data available
                echo "<td data-editable='true' class='editable' data-id=" . $part_id . " data-column=" . $column_data . " data-table_name=" . $table_name . " data-id_field=" . $id_field . ">" . $row[$column_data] . "</td>";
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
            data-cookie-id-table="PartsStockHistoryTableState"
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
            data-cookie-id-table="PartInBomsTableState"
            data-max-moving-rows="100"
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