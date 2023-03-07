<?php
function buildTable($column_names, $nice_columns, $result, $width="100%"){
    echo '<div style="overflow-x:auto;">';
    echo '<table class="table table-striped table-hover" style="width: '.$width.'">';
    
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
            echo "<td>".$row[$column_data]."</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
}

function buildBackordersTable($result, $db_columns, $nice_columns, $width="100%"){
    echo '<div style="overflow-x:auto;">';
    echo '<table class="table table-striped table-hover" style="width: '.$width.'">';
    
    // Table headers
    echo "<thead>";
    echo "<tr>";
    foreach ($nice_columns as $column_header){
        echo "<th>$column_header</th>";
    }
    echo "</tr>";
    echo "</thead>";


    // Table rows
    foreach ($result as $row) {
        echo "<tr>";
        foreach ($db_columns as $column_data) {
            if ($column_data == "customer_po") { // Make links for PO Numbers
                $po_number = $row[$column_data];
                echo "<td><a href='show-backorder.php?po=$po_number'>".$row[$column_data]."</a></td>";
            } else {
            echo "<td>".$row[$column_data]."</td>";
            }
            }
        echo "</tr>";
    }

    echo "</table>";
    echo "</div>";
}

function buildBomTable($result, $db_columns, $nice_columns, $width="100%"){
    echo '<div style="overflow-x:auto;">';
    echo '<table class="table table-striped table-hover" style="width: '.$width.'">';
    
    // Table headers
    echo "<thead>";
    echo "<tr>";
    foreach ($nice_columns as $column_header){
        echo "<th>$column_header</th>";
    }
    echo "</tr>";
    echo "</thead>";

    echo "<tbody>";
    // Table rows
    foreach ($result as $row) {
        echo "<tr data-id=".$row['bom_id'].">";
        foreach ($db_columns as $column_data) {
            if ($column_data == "bom_name") { // Make links for BOM names, use ID for referral
                $bom_id = $row['bom_id'];
                echo "<td><a href='show-bom.php?id=$bom_id'>".$row[$column_data]."</a></td>";
            } else {
            echo "<td>".$row[$column_data]."</td>";
            }
            }
        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";
    echo "</div>";
}

function buildPartsTable($result, $db_columns, $nice_columns, $total_stock, $conn, $width="100%"){
    echo '<div style="overflow-x:auto;">';
    echo '<table id="parts-table" class="table table-striped table-hover table-bordered table-resizable" style="width: '.$width.'">';
    
    // Table headers
    echo "<thead>";
    echo "<tr>";
    foreach ($nice_columns as $column_header){
        echo "<th>$column_header</th>";
    }
    echo "</tr>";
    echo "</thead>";


    // Table rows
    foreach ($result as $row) {
        echo "<tr>";
        foreach ($db_columns as $column_data) {
            if ($column_data == 'total_stock'){ 
                $part_id = $row['part_id'];
                // Get total stock
                $stock = getStockLevels($conn, $part_id);
                $total_stock = getTotalStock($stock);
                // Display total stock number as link to showing stock levels
                echo "<td><a href='show-stock.php?part_id=$part_id'>".$total_stock."</a></td>";
            }
            else { // Any other table data available
            echo "<td class='editable'>".$row[$column_data]."</td>";
            }
            }
        
        echo "</tr>";
    }

    echo "</table>";
    echo "</div>";
}