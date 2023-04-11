<?php
include '../config/credentials.php';
include 'SQL.php';
$ids = $_POST['ids'];
$table = $_POST['table'];
$column = $_POST['column'];

$success = 0;
$fail = 0;

// It's an array, even if it has just one entry, so I need to iterate here
foreach ($ids as $id) {
    try {
        $conn = connectToSQLDB($hostname, $username, $password, $database_name);
        deleteRowById($conn, $id, $table, $column);
        $success += 1;
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage(), "\n";
        $fail += 1;
    }
}
// echo $success;

//TODO: Properly return successful and failed parts