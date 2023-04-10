<?php
include '../config/credentials.php';
include 'SQL.php';
$part_ids = $_POST['part_ids'];
$success = 0;
$fail = 0;

// It's an array, even if it has just one entry, so I need to iterate here
foreach ($part_ids as $part_id) {
    try {
        $conn = connectToSQLDB($hostname, $username, $password, $database_name);
        deletePart($conn, $part_id);
        $success += 1;
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage(), "\n";
        $fail += 1;
    }
}

// Properly return successfuly and failed parts