<?php
$title = 'Categories';
require_once('head.html');
include 'config/credentials.php';
include 'inline-processing.php';
include 'lib/SQL.php';
include 'lib/forms.php';
$table_name = "part_categories";

?>

<style>
    .fixed-bottom {
        height: 100%;
        /* Set the height as needed */
    }
</style>
<div class="container-fluid">
    <?php require_once('navbar.php'); ?>
    <br>
    <h4>Categories</h4>