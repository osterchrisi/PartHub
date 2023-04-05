<?php
echo 1;
include '../config/credentials.php';
include 'SQL.php';
echo 2;
$part_id = $_GET['part_id'];
var_dump($_GET);
echo $part_id;

try {
    $conn = connectToSQLDB($hostname, $username, $password, $database_name);
    deletePart($conn, $part_id);
} catch (Exception $e) {
    echo "<br>Error: " . $e->getMessage();
}