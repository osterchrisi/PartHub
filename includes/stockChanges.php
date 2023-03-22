<?php
include "session.php";
include "../config/credentials.php";
include "SQL.php";

$conn = connectToSQLDB($hostname, $username, $password, $database_name);
$test = getUserName($conn);

$s = 'I got this value from you: ';
$quant = $_POST['quant'];
$d = $_POST['desc'];
// $id1 = $_POST['id'];
$user_id = $_SESSION['user_id'];
$part_id = $_POST['part_id'];

stockChange($conn, $part_id);

echo json_encode([$s, $quant, $d, $id2, $test, $part_id]);