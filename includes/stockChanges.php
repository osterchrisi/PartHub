<?php
include "session.php";
include "../config/credentials.php";
include "SQL.php";

$conn = connectToSQLDB($hostname, $username, $password, $database_name);
$test = getUserName($conn);

$s = 'I got this value from you: ';
$quantity = $_POST['quant'];
$d = $_POST['desc'];
// $id1 = $_POST['id']; // currently use user_id from $_SESSION array
$user_id = $_SESSION['user_id'];
$part_id = $_POST['part_id'];
$from_location = '5';
$to_location = '6';
$datetime = 'NULL';

$result = stockChange($conn, $part_id, $from_location, $to_location, $quantity, $d, $datetime, $user_id);

echo json_encode([$result]);