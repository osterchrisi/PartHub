<?php
$basename = basename(__FILE__);
$title = 'Sign Up';
require_once('../includes/head.html');
include '../config/credentials.php';
include '../includes/SQL.php';

$table_name = "users";
$conn = connectToSQLDB($hostname, $username, $password, $database_name);
?>

<div class="container-fluid">
  <?php require_once('../includes/navbar.php'); ?>
  <br>
  <h4>Log in to your PartHub account</h4>
</div>

//TODO: Check if user already logged in
//TODO: Let user log in