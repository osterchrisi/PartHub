<?php
include "session.php";
include "../config/credentials.php";
include "SQL.php";
include "helpers.php";

$conn = connectToSQLDB($hostname, $username, $password, $database_name);
$test = getUserName($conn);

//TODO: Sanitize and validate data before doing anything. Better yet in the JS section, so user
//TODO: can know about it!

// Determine type of change
$change = $_POST['change'];

// Gather variables
$quantity = $_POST['quantity'];
$to_location = $_POST['to_location'];
$comment = $_POST['comment'];
// $id1 = $_POST['id']; // currently use user_id from $_SESSION array
$user_id = $_SESSION['user_id'];
$part_id = $_POST['part_id'];
$from_location = $_POST['from_location'];
$datetime = 'NULL'; //* Table record gets current timestamp in SQL query -> might not be ideal for users not in same timezone as DB

// Get all dem stock levels
$stock_levels = $_SESSION['stock_levels'];
// Get current stock level for to_location
$current_stock_level_to = getCurrentStock($stock_levels, $to_location);
// Get current stock level for from_location
$current_stock_level_from = getCurrentStock($stock_levels, $from_location);

if ($change == 1) { // Add Stock
    $quantity += $current_stock_level_to;
    changeQuantity($conn, $part_id, $quantity, $to_location);
}
elseif ($change == -1) { // Reduce Stock
    $quantity = $current_stock_level_to - $quantity;
    changeQuantity($conn, $part_id, $quantity, $to_location);
}
elseif ($change == 0) { // Move Stock
    // Add stock for the to_location
    $quantity += $current_stock_level_to;
    changeQuantity($conn, $part_id, $quantity, $to_location);
    $quantity -= $current_stock_level_to; //* Kinda silly 

    // Remove stock from the from_location
    $quantity = $current_stock_level_from - $quantity;
    changeQuantity($conn, $part_id, $quantity, $from_location);
}

// Make record in stock_level_change_history table
$hist_id = stockChange($conn, $part_id, $from_location, $to_location, $quantity, $comment, $datetime, $user_id);

echo json_encode([$hist_id]);