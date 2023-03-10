<?php
$title = 'Locations';
require_once('../includes/head.html');
include '../config/credentials.php';
include '../includes/inline-processing.php';
include '../includes/SQL.php';
include '../includes/forms.php';
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