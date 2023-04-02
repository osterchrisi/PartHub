<?php
// User settings page
$basename = basename(__FILE__);
$title = 'Categories';
require_once('../includes/head.html');
include '../config/credentials.php';
include '../includes/SQL.php';
include '../includes/forms.php';

$table_name = "users";
$conn = connectToSQLDB($hostname, $username, $password, $database_name);
// echo getCurrentUser();
?>

<div class="container-fluid">
  <?php require_once('../includes/navbar.php'); ?>
  <br>
  <h4>User Settings</h4>
  <?php include "../includes/user-settings.php" ?>
</div>