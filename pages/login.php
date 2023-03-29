<?php
$basename = basename(__FILE__);
$title = 'Log In';
require_once('../includes/head.html');
include '../config/credentials.php';
include '../includes/SQL.php';

$table_name = "users";
$conn = connectToSQLDB($hostname, $username, $password, $database_name);

require_once('../includes/navbar.php'); ?>

<div class="d-flex full-height flex-grow-1 justify-content-center align-items-center">
  <div class="greeting d-flex align-items-center">
    <table class="table table-borderless text-center mx-auto w-auto" style="borders: false">
      <thead>
        <tr>
          <th>
            <h4>Log in to your PartHub account</h4>
          </th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td style='text-align:left'>
            <form method="post" action="../includes/verify-login.php">
              <label for="inputEmail" class="form-label">Email</label>
              <input type="email" class="form-control" id="inputEmail" name="email">
              <label for="inputPassword" class="form-label">Password</label>
              <input type="password" class="form-control" id="inputPassword" name="passwd">
          </td>
        </tr>
        <tr>
          <td>
            <button type="submit" class="btn btn-primary">Sign in</button>
            </form>

          </td>
        </tr>
        <tr>
          <td>Don't have an account yet? Sign up for free <a href="signup.php">here</a>!
          </td>
        </tr>
      <tbody>
    </table>
  </div>
</div>

<?php


//TODO: Check if user already logged in
//TODO: Let user log in
?>