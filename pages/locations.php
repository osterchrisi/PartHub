<?php
$basename = basename(__FILE__);
$title = 'Locations';
require_once('../includes/head.html');

include '../config/credentials.php';
include '../includes/inline-processing.php';
include '../includes/SQL.php';
include '../includes/forms.php';
include '../includes/tables.php';
$table_name = "location_names";

?>

<style>
    .fixed-bottom {
        height: 100%;
        /* Set the height as needed */
    }
</style>
<div class="container-fluid">
    <?php require_once('../includes/navbar.php'); ?>
    <br>
    <h4>Storage Locations</h4>

    <?php  
    $conn = connectToSQLDB($hostname, $username, $password, $database_name);
    $loc = getLocations($conn);
    // print_r($loc);
    $column_names = array('location_id', 'location_name', 'location_description');
    $nice_columns = array('ID', 'Name', 'Description');
    buildTable($column_names, $nice_columns, $loc);
    ?>