<?php
include '../config/credentials.php';
include 'SQL.php';
$part_ids = $_POST['part_ids'];

// It's an array, even if it has just one entry, so I need to iterate here
foreach ($part_ids as $part_id) {
    try {
        echo "Deleting $part_id ";
        // $conn = connectToSQLDB($hostname, $username, $password, $database_name);
        // deletePart($conn, $part_id);
    } catch (Exception $e) {
        echo "<br>Error: " . $e->getMessage();
    }
}