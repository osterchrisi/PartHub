<?php
// Footprint page
$basename = basename(__FILE__);
$title = 'File Upload Test';
require_once('../includes/head.html');
include '../config/credentials.php';
include '../includes/SQL.php';
include '../includes/forms.php';
$table_name = "part_categories";

?>
<div class="container-fluid">
    <?php require_once('../includes/navbar.php'); ?>
    <br>
    <h4>File Upload Test</h4>

    <form action="../includes/import-csv.php" method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="formFile" class="form-label">Select CSV file to upload. Maximum 1000 lines per file</label>
            <input class="form-control" type="file" id="formFile" name="csvFile" accept=".csv">
        </div>
        <button type="submit" class="btn btn-primary" name="apply">Upload</button>
    </form>