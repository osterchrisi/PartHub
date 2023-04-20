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

// Determine type of change
$change = $_POST['change'];

// Gather variables
$quantity = $_POST['quantity'];

$to_location = $_POST['to_location'];
if ($to_location == 'NULL') {
    $to_location = NULL;
}

$from_location = $_POST['from_location'];
if ($from_location == 'NULL') {
    $from_location = NULL;
}

$comment = $_POST['comment'];
$user_id = $_SESSION['user_id'];
$part_id = $_POST['part_id'];

// Get all dem stock levels from the $_SESSION array
$stock_levels = $_SESSION['stock_levels'];
$current_stock_level_to = getCurrentStock($stock_levels, $to_location);
$current_stock_level_from = getCurrentStock($stock_levels, $from_location);

echo "Current stock: $current_stock_level_from\n";

// Change the stock level entries that exist for this part in that location
if ($change == 1) { // Add Stock
    $new_quantity = $current_stock_level_to + $quantity;
    $stock_level_id = changeQuantity($conn, $part_id, $new_quantity, $to_location);
}
elseif ($change == -1) { // Reduce Stock
    $new_quantity = $current_stock_level_from - $quantity;
    $stock_level_id = changeQuantity($conn, $part_id, $new_quantity, $from_location);
    echo "new quantity: $new_quantity\n";
    if ($new_quantity < 0) {
        echo "Not enough stock for $part_id\n";
    }
}
elseif ($change == 0) { // Move Stock
    // Add stock for the to_location
    $to_quantity = $current_stock_level_to + $quantity;
    $stock_level_id = changeQuantity($conn, $part_id, $to_quantity, $to_location);

    // Remove stock from the from_location
    $from_quantity = $current_stock_level_from - $quantity;
    $stock_level_id = changeQuantity($conn, $part_id, $from_quantity, $from_location);
}

// Make record in stock_level_change_history table
$hist_id = stockChange($conn, $part_id, $from_location, $to_location, $quantity, $comment, $user_id);

$stock = getStockLevels($conn, $part_id);
$total_stock = getTotalStock($stock);

//TODO: This ist part of my hicky hacky solution to update the stock level in the parts_table after updating
echo json_encode([$hist_id, $stock_level_id, $total_stock]);