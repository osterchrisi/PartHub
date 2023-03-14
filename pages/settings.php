<?php
$title = 'Categories';
require_once('../includes/head.html');
include '../config/credentials.php';
include '../includes/SQL.php';
include '../includes/forms.php';
$table_name = "part_categories";

?>

<div class="container-fluid">
  <?php require_once('../includes/navbar.php'); ?>
  <br>
  <h4>Settings</h4>

  <?php
  $conn = connectToSQLDB($hostname, $username, $password, $database_name);

  ?>
</div>