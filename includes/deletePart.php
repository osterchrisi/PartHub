<?php
include '../config/credentials.php';
include 'SQL.php';
$part_ids = $_GET['part_ids'];
var_dump($_GET);
echo "<br>";
// It's an array, even if it has just one entry, so I need to iterate here
foreach ($part_ids as $part_id) {
    try {
        echo "Deleting $part_id <br>";
        // $conn = connectToSQLDB($hostname, $username, $password, $database_name);
        // deletePart($conn, $part_id);
    } catch (Exception $e) {
        echo "<br>Error: " . $e->getMessage();
    }
}