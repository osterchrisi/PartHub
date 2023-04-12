<?php
/**
 * @file Deletes a row in a table
 * @param int ids Array of IDs to delete
 * @param string table_name Name of the table in the database
 * @param string column Name of the column that holds the ID, e.g. part_id
 */

include '../config/credentials.php';
include 'SQL.php';
$ids = $_POST['ids'];
$table = $_POST['table'];
$column = $_POST['column'];

$success = 0;
$fail = 0;

// It's an array even if it has just one entry, so I need to iterate here
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

//TODO: Properly return successful and failed parts