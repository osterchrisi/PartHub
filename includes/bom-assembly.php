<?php
/**
 * @file This script is not documented yet
 */

include '../config/credentials.php';
include 'SQL.php';
$ids = $_POST['ids'];
$quantity = $_POST['quantity'];

$conn = connectToSQLDB($hostname, $username, $password, $database_name);

$success = 0;
$fail = 0;

echo var_dump($_POST);

// It's an array even if it has just one entry, so I need to iterate here
foreach ($ids as $id) {
    try {
        // deleteRowById($conn, $id, $table, $column);
        echo "Assembling $id\n";
        $success += 1;
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage(), "\n";
        $fail += 1;
    }
}

//TODO: Properly return successful and failed assemblies