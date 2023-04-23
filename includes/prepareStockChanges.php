<?php
/**
 * Add, reduce or move stock
 * 
 * Returns an array of arrays:
 * The $changes array that has ALL changes requested.
 * The $negative_stock array that contains only entries for stock changes that would result in negative stock.
 * The $negative_stock_table is an HTML string that contains a table built out of the negative_stock array
 */

require_once "session.php";
require_once "../config/credentials.php";
require_once "SQL.php";
require_once "helpers.php";
require_once 'get.php';
require_once 'tables.php';

$conn = connectToSQLDB($hostname, $username, $password, $database_name);
$test = getUserName($conn);

// //TODO: Sanitize and validate data before doing anything. Better yet in the JS section, so user
// //TODO: can know about it!

// Access stock changes to prepare
$requested_changes = $_POST["stock_changes"];

// Initialize the changes array and negative stock array
$changes = array();
$negative_stock = array();

//* Fill arrays with all requested changes
foreach ($requested_changes as $requested_change) {

    // Gather variables
    $change = $requested_change['change'];
    $bom_id = $requested_change['bom_id'];
    $part_id = $requested_change['part_id'];
    $quantity = $requested_change['quantity'];

    $to_location = $requested_change['to_location'];
    if ($to_location == 'NULL') {
        $to_location = NULL;
    }

    $from_location = $requested_change['from_location'];
    if ($from_location == 'NULL') {
        $from_location = NULL;
    }

    $comment = $requested_change['comment'];
    $permission = $requested_change['permission'];


    // Get all dem stock levels for currently iterated part
    $stock_levels = getStockLevels($conn, $part_id);
    $current_stock_level_to = getCurrentStock($stock_levels, $to_location);
    $current_stock_level_from = getCurrentStock($stock_levels, $from_location);

    //* Collect changes to be made
    if ($change == 1) { // Add Stock
        $new_quantity = $current_stock_level_to + $quantity;
        //Add entry to changes array
        $changes[] = array(
            'bom_id' => $bom_id,
            'part_id' => $part_id,
            'quantity' => $quantity,
            'to_location' => $to_location,
            'from_location' => $from_location,
            'change' => $change,
            'new_quantity' => $new_quantity,
            'comment' => $comment,
            'status' => 'gtg'
        );
    }
    elseif ($change == -1) { // Reduce Stock
        $new_quantity = $current_stock_level_from - $quantity;

        // Stock would go negative
        if ($new_quantity < 0 && $permission == false) {
            $changes[] = array(
                'bom_id' => $bom_id,
                'part_id' => $part_id,
                'quantity' => $quantity,
                'to_location' => $to_location,
                'from_location' => $from_location,
                'change' => $change,
                'new_quantity' => $new_quantity,
                'comment' => $comment,
                'status' => 'permission_required'
            );
            //Add entry to negative stock array
            $negative_stock[] = array(
                'bom_id' => $bom_id,
                'part_id' => $part_id,
                'quantity' => $quantity,
                'to_location' => $to_location,
                'from_location' => $from_location,
                'change' => $change,
                'new_quantity' => $new_quantity,
                'comment' => $comment,
                'status' => 'permission_required'
            );
        }
        else {
            //Add entry to changes array
            $changes[] = array(
                'bom_id' => $bom_id,
                'part_id' => $part_id,
                'quantity' => $quantity,
                'to_location' => $to_location,
                'from_location' => $from_location,
                'change' => $change,
                'new_quantity' => $new_quantity,
                'comment' => $comment,
                'status' => 'gtg'
            );
        }
    }
    elseif ($change == 0) { // Move Stock
        // New quantity in 'to location'
        $to_quantity = $current_stock_level_to + $quantity;

        // New quantity in 'from_location'
        $from_quantity = $current_stock_level_from - $quantity;

        // Stock in 'from location' goes negative
        if ($from_quantity < 0 && $permission == false) {
            //Add entry to changes array
            $changes[] = array(
                'bom_id' => $bom_id,
                'part_id' => $part_id,
                'quantity' => $quantity,
                'to_location' => $to_location,
                'from_location' => $from_location,
                'change' => $change,
                'to_quantity' => $to_quantity,
                'from_quantity' => $from_quantity,
                'comment' => $comment,
                'status' => 'permission_required'
            );
            //Add entry to negative stock array
            $negative_stock[] = array(
                'bom_id' => $bom_id,
                'part_id' => $part_id,
                'quantity' => $quantity,
                'to_location' => $to_location,
                'from_location' => $from_location,
                'change' => $change,
                'to_quantity' => $to_quantity,
                'from_quantity' => $from_quantity,
                'comment' => $comment,
                'status' => 'permission_required'
            );
        }
        else {
            //Add entry to changes array
            $changes[] = array(
                'bom_id' => $bom_id,
                'part_id' => $part_id,
                'quantity' => $quantity,
                'to_location' => $to_location,
                'from_location' => $from_location,
                'change' => $change,
                'to_quantity' => $to_quantity,
                'from_quantity' => $from_quantity,
                'comment' => $comment,
                'status' => 'gtg'
            );
        }
    }

}

//* Make the actual stock change entries and stock change history entries
//TODO: Would be maybe nice to extract this to different file?

//* If there are stock shortages, produce table and send back to user
if (!empty($negative_stock)) {
    $column_names = array('bom_id', 'part_id', 'quantity', 'from_location', 'new_quantity');
    $nice_columns = array('BOM ID', 'Part ID', 'Quantity needed', 'Location', 'Resulting Quantity');
    $negative_stock_table = buildHTMLTable($column_names, $nice_columns, $negative_stock);
    echo json_encode(array(
        'changes' => $changes,
        'negative_stock' => $negative_stock,
        'negative_stock_table' => $negative_stock_table,
        'status' => 'permission_requested'));
    exit;
}
//* If no user permission is necessary
else {
    foreach ($changes as $commit_change) {
        // $dump = print_r($commit_change);
        // echo $dump;
        // First extract variables
        $part_id = $commit_change['part_id'];
        $bom_id = $commit_change['bom_id'];
        $change = $commit_change['change'];
        
        $quantity = $commit_change['quantity'];
        $to_quantity = $commit_change['to_quantity'];
        $from_quantity = $commit_change['from_quantity'];
        
        
        $new_quantity = $commit_change['new_quantity'];
        $to_location = $commit_change['to_location'];
        $from_location = $commit_change['from_location'];
        $comment = $commit_change['comment'];
        $status = $commit_change['status'];

        if ($change == 1) { // Add Stock
            // Make records in stock_level and stock_level_change_history tables
            $stock_level_id = changeQuantity($conn, $part_id, $new_quantity, $to_location);
            $hist_id = stockChange($conn, $part_id, $from_location, $to_location, $quantity, $comment, $user_id);

            // Calculate new stock
            $stock = getStockLevels($conn, $part_id);
            $total_stock = getTotalStock($stock);

            //TODO: This ist part of my hicky hacky solution to update the stock level in the parts_table after updating
            // Report back for updating tables
            $result = [$hist_id, $stock_level_id, $total_stock];
            echo json_encode(array(
                'changes' => array(),
                'negative_stock' => array(),
                'negative_stock_table' => array(),
                'status' => 'success',
                'result' => $result));
        }
        elseif ($change == -1) { // Reduce Stock
            $stock_level_id = changeQuantity($conn, $part_id, $new_quantity, $from_location);
            $hist_id = stockChange($conn, $part_id, $from_location, $to_location, $quantity, $comment, $user_id);

            // Calculate new stock
            $stock = getStockLevels($conn, $part_id);
            $total_stock = getTotalStock($stock);

            //TODO: This ist part of my hicky hacky solution to update the stock level in the parts_table after updating
            // Report back for updating tables
            $result = [$hist_id, $stock_level_id, $total_stock];
            echo json_encode(array(
                'changes' => array(),
                'negative_stock' => array(),
                'negative_stock_table' => array(),
                'status' => 'success',
                'result' => $result));
        }
        elseif ($change == 0) {
            // print_r($commit_change);
            // First add stock in 'to location'
            $stock_level_id = changeQuantity($conn, $part_id, $to_quantity, $to_location);

            // Then reduce stock in 'from location'
            $stock_level_id = changeQuantity($conn, $part_id, $from_quantity, $from_location);

            // History entry
            $hist_id = stockChange($conn, $part_id, $from_location, $to_location, $quantity, $comment, $user_id);

            // Reporting stock so that it can be updated in the origin table

            // Calculate new stock
            $stock = getStockLevels($conn, $part_id);
            $total_stock = getTotalStock($stock);

            //TODO: This ist part of my hicky hacky solution to update the stock level in the parts_table after updating
            $result = [$hist_id, $stock_level_id, $total_stock];
            echo json_encode(array(
                'changes' => array(),
                'negative_stock' => array(),
                'negative_stock_table' => array(),
                'status' => 'success',
                'result' => $result));
        }
    }
}