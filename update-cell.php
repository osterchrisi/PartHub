<?php
include 'config/credentials.php';
include 'lib/SQL.php';


$part_id = isset($_GET['part_id']) ? $_GET['part_id'] : '';
$column = isset($_GET['column']) ? $_GET['column'] : '';
$table_name = isset($_GET['table_name']) ? $_GET['table_name'] : '';
$new_value = isset($_GET['new_value']) ? $_GET['new_value'] : '';

$conn = connectToSQLDB($hostname, $username, $password, $database_name);

try {
    updateRow($conn, $part_id, $column, $table_name, $new_value);
} catch (Exception $e) {
    echo "<br>Error: " . $e->getMessage();
}

echo "<br>" . "success!";

?>