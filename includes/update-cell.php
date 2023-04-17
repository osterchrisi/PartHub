/**
 * @file update-cell.php Update an editable table cell in a table
 */
<?php
include '../config/credentials.php';
include 'SQL.php';

// Get variables
$id = isset($_GET['id']) ? $_GET['id'] : '';
$column = isset($_GET['column']) ? $_GET['column'] : '';
$table_name = isset($_GET['table_name']) ? $_GET['table_name'] : '';
$new_value = isset($_GET['new_value']) ? $_GET['new_value'] : '';
$id_field = isset($_GET['id_field']) ? $_GET['id_field'] : '';

// Connect to database
$conn = connectToSQLDB($hostname, $username, $password, $database_name);

try {
    updateRow($conn, $id, $id_field, $column, $table_name, $new_value);
} catch (Exception $e) {
    echo "<br>Error: " . $e->getMessage();
}

echo "<br>" . "success!";

?>