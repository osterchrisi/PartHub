<?php
// Add, reduce or move stock
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

// Iterate over each part
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

    //* Change the stock level entries that exist for this part in that location
    if ($change == 1) { // Add Stock
        $new_quantity = $current_stock_level_to + $quantity;
        // $stock_level_id = changeQuantity($conn, $part_id, $new_quantity, $to_location);
        //Add entry to changes array
        $changes[] = array(
            'bom_id' => $bom_id,
            'part_id' => $part_id,
            'quantity' => $quantity,
            'to_location' => $to_location,
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
                'from_location' => $from_location,
                'change' => $change,
                'new_quantity' => $new_quantity,
                'comment' => $comment,
                'status' => 'permission_required'
            );
        }
        else {
            // $stock_level_id = changeQuantity($conn, $part_id, $new_quantity, $from_location);
            //Add entry to changes array
            $changes[] = array(
                'bom_id' => $bom_id,
                'part_id' => $part_id,
                'quantity' => $quantity,
                'from_location' => $from_location,
                'change' => $change,
                'new_quantity' => $new_quantity,
                'comment' => $comment,
                'status' => 'gtg'
            );
            // echo json_encode($changes);
        }
    }
    elseif ($change == 0) { // Move Stock
        // Add stock in 'to location'
        $to_quantity = $current_stock_level_to + $quantity;
        // $stock_level_id = changeQuantity($conn, $part_id, $to_quantity, $to_location);
        $changes[] = array(
            'bom_id' => $bom_id,
            'part_id' => $part_id,
            'quantity' => $quantity,
            'to_location' => $to_location,
            'change' => $change,
            'new_quantity' => $to_quantity,
            'comment' => $comment,
            'status' => 'gtg'
        );

        // Remove stock in 'from_location'
        $from_quantity = $current_stock_level_from - $quantity;

        // Stock in 'from location' goes negative
        if ($from_quantity < 0 && $permission == false) {
            //Add entry to changes array
            $changes[] = array(
                'bom_id' => $bom_id,
                'part_id' => $part_id,
                'quantity' => $quantity,
                'from_location' => $from_location,
                'change' => $change,
                'new_quantity' => $from_quantity,
                'comment' => $comment,
                'status' => 'permission_required'
            );
            //Add entry to negative stock array
            $negative_stock[] = array(
                'bom_id' => $bom_id,
                'part_id' => $part_id,
                'quantity' => $quantity,
                'from_location' => $from_location,
                'change' => $change,
                'new_quantity' => $from_quantity,
                'comment' => $comment,
                'status' => 'permission_required'
            );
        }
        else {
            // $stock_level_id = changeQuantity($conn, $part_id, $new_quantity, $from_location);
            //Add entry to changes array
            $changes[] = array(
                'bom_id' => $bom_id,
                'part_id' => $part_id,
                'quantity' => $quantity,
                'from_location' => $from_location,
                'change' => $change,
                'new_quantity' => $from_quantity,
                'comment' => $comment,
                'status' => 'gtg'
            );
        }
        // $stock_level_id = changeQuantity($conn, $part_id, $from_quantity, $from_location);
    }

}

// if (empty($negative_stock)) {
//     echo "All is well and these are the changes to commit\n";
//     echo json_encode($changes);
// }
// else {
//     echo "Some stuff is out of stock, these are the changes that need permission\n";
//     echo json_encode($negative_stock);
// }
if (!empty($negative_stock)){
$column_names = array('bom_id', 'part_id', 'quantity', 'from_location', 'new_quantity');
$nice_columns = array ('BOM ID', 'Part ID', 'Quantity needed', 'Location', 'Resulting Quantity');
$negative_stock_table = buildHTMLTable($column_names, $nice_columns, $negative_stock);}
echo json_encode(array('changes' => $changes, 'negative_stock' => $negative_stock, 'negative_stock_table' => $negative_stock_table));

//*This is original code:
//* Make record in stock_level_change_history table
// $hist_id = stockChange($conn, $part_id, $from_location, $to_location, $quantity, $comment, $user_id);

// $stock = getStockLevels($conn, $part_id);
// $total_stock = getTotalStock($stock);

// //TODO: This ist part of my hicky hacky solution to update the stock level in the parts_table after updating
// echo json_encode([$hist_id, $stock_level_id, $total_stock, 'status' => 'success']);