<?php
include "session.php";
include "../config/credentials.php";
include "SQL.php";

$conn = connectToSQLDB($hostname, $username, $password, $database_name);
$test = getUserName($conn);

// Gather variables
$quantity = $_POST['quantity'];
$to_location = $_POST['to_location'];
$comment = $_POST['comment'];
// $id1 = $_POST['id']; // currently use user_id from $_SESSION array
$user_id = $_SESSION['user_id'];
$part_id = $_POST['part_id'];
$from_location = '5';
$datetime = 'NULL'; //* Table record gets current timestamp in SQL query -> might not be ideal for users not in same timezone as DB

//Get current stock in location
// $current_stock = ....

// Update stock in location
$change_id = 0;
$change_id = changeQuantity($conn, $part_id, $quantity, $to_location);

// Make record in stock_level_change_history table
$hist_id = stockChange($conn, $part_id, $from_location, $to_location, $quantity, $comment, $datetime, $user_id);

echo json_encode([$hist_id, $change_id]);