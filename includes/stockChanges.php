<?php
// Add, reduce or move stock
require_once "session.php";
require_once "../config/credentials.php";
require_once "SQL.php";
require_once "helpers.php";
require_once 'get.php';

$conn = connectToSQLDB($hostname, $username, $password, $database_name);
$test = getUserName($conn);

// //TODO: Sanitize and validate data before doing anything. Better yet in the JS section, so user
// //TODO: can know about it!

// $requested_changes = json_decode($_POST["stock_changes"], true); // The 'true' statement is for returning an associative array
// echo json_encode($_POST);
$requested_changes = $_POST["stock_changes"];
// echo json_encode($requested_changes);

// Initialize the changes array and negative stock array
$changes = array();
$negative_stock = array();

// Iterate over each part
foreach ($requested_changes as $requested_change) {
    // echo json_encode($requested_change);

    // Determine type of change
    $change = $requested_change['change'];

    // Gather variables
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
    $user_id = $_SESSION['user_id']; //! Think I could get rid of this here
    $part_id = $requested_change['part_id'];
    $permission = $requested_change['permission'];

    // Get all dem stock levels from the $_SESSION array
    //! //TODO: Think I can get rid of the $_SESSION array after iteration approach
    $stock_levels = getStockLevels($conn, $part_id);
    // $stock_levels = $_SESSION['stock_levels'];
    $current_stock_level_to = getCurrentStock($stock_levels, $to_location);
    $current_stock_level_from = getCurrentStock($stock_levels, $from_location);

    //* Change the stock level entries that exist for this part in that location
    if ($change == 1) { // Add Stock
        $new_quantity = $current_stock_level_to + $quantity;
        // $stock_level_id = changeQuantity($conn, $part_id, $new_quantity, $to_location);
        //Add entry to changes array
        $changes[] = array(
            'part_id' => $part_id,
            'quantity' => $quantity,
            'to_location' => $to_location,
            'change' => $change,
            'new_quantity' => $new_quantity
        );
    }
    elseif ($change == -1) { // Reduce Stock
        $new_quantity = $current_stock_level_from - $quantity;

        // Stock would go negative
        if ($new_quantity < 0 && $permission == false) {
            $data = array(
                'status' => 'permission_required',
                'part_id' => $part_id,
                'new_quantity' => $new_quantity,
                'stock_level' => $current_stock_level_from
            );
            // $json_data = json_encode($data);
            // echo $json_data;
            // exit;
            //Add entry to changes array
            $changes[] = array(
                'part_id' => $part_id,
                'quantity' => $quantity,
                'from_location' => $from_location,
                'change' => $change,
                'new_quantity' => $new_quantity
            );
            //Add entry to negative stock array
            $negative_stock[] = array(
                'part_id' => $part_id,
                'quantity' => $quantity,
                'from_location' => $from_location,
                'change' => $change,
                'new_quantity' => $new_quantity
            );
            // echo json_encode($changes);
        }
        else {
            // $stock_level_id = changeQuantity($conn, $part_id, $new_quantity, $from_location);
            //Add entry to changes array
            $changes[] = array(
                'part_id' => $part_id,
                'quantity' => $quantity,
                'from_location' => $from_location,
                'change' => $change,
                'new_quantity' => $new_quantity
            );
            // echo json_encode($changes);
        }
    }
    elseif ($change == 0) { // Move Stock
        // Add stock in 'to location'
        $to_quantity = $current_stock_level_to + $quantity;
        // $stock_level_id = changeQuantity($conn, $part_id, $to_quantity, $to_location);
        $changes[] = array(
            'part_id' => $part_id,
            'quantity' => $quantity,
            'to_location' => $to_location,
            'change' => $change,
            'new_quantity' => $to_quantity
        );

        // Remove stock in 'from_location'
        $from_quantity = $current_stock_level_from - $quantity;

        // Stock in 'from location' goes negative
        if ($from_quantity < 0 && $permission == false) {
            $data = array(
                'status' => 'permission_required',
                'part_id' => $part_id,
                'new_quantity' => $new_quantity,
                'stock_level' => $current_stock_level_from
            );
            //Add entry to changes array
            $changes[] = array(
                'part_id' => $part_id,
                'quantity' => $quantity,
                'from_location' => $from_location,
                'change' => $change,
                'new_quantity' => $from_quantity
            );
            //Add entry to negative stock array
            $negative_stock[] = array(
                'part_id' => $part_id,
                'quantity' => $quantity,
                'from_location' => $from_location,
                'change' => $change,
                'new_quantity' => $from_quantity
            );
        }
        else {
            // $stock_level_id = changeQuantity($conn, $part_id, $new_quantity, $from_location);
            //Add entry to changes array
            $changes[] = array(
                'part_id' => $part_id,
                'quantity' => $quantity,
                'from_location' => $from_location,
                'change' => $change,
                'new_quantity' => $from_quantity
            );
        }
        // $stock_level_id = changeQuantity($conn, $part_id, $from_quantity, $from_location);
    }

}

echo json_encode(array('changes' => $changes, 'negative_stock' => $negative_stock));


//*This is original code:
//* Make record in stock_level_change_history table
// $hist_id = stockChange($conn, $part_id, $from_location, $to_location, $quantity, $comment, $user_id);

// $stock = getStockLevels($conn, $part_id);
// $total_stock = getTotalStock($stock);

// //TODO: This ist part of my hicky hacky solution to update the stock level in the parts_table after updating
// echo json_encode([$hist_id, $stock_level_id, $total_stock, 'status' => 'success']);