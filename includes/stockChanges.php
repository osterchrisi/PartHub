<?php
include "session.php";
include "../config/credentials.php";
include "SQL.php";

$conn = connectToSQLDB($hostname, $username, $password, $database_name);
$test = getUserName($conn);


// Gather variables
$quantity = $_POST['quantity'];
$comment = $_POST['comment'];
// $id1 = $_POST['id']; // currently use user_id from $_SESSION array
$user_id = $_SESSION['user_id'];
$part_id = $_POST['part_id'];
$from_location = '5';
$to_location = '6';
$datetime = 'NULL';

// Make record in stock_level_change_history table
$result = stockChange($conn, $part_id, $from_location, $to_location, $quantity, $comment, $datetime, $user_id);

echo json_encode([$result]);