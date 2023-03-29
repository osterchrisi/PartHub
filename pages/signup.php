<?php
$basename = basename(__FILE__);
$title = 'Sign up';
require_once('../includes/head.html');
include '../config/credentials.php';
include '../includes/SQL.php';

$table_name = "users";
$conn = connectToSQLDB($hostname, $username, $password, $database_name);

require_once('../includes/navbar.php'); ?>

<div class="d-flex full-height flex-grow-1 justify-content-center align-items-center">
  <div class="greeting d-flex align-items-center">
  <form method="post" action="../includes/captcha.php">
    <table class="table table-borderless text-center mx-auto w-auto" style="borders: false">
      <thead>
        <tr>
          <th>
            <h4>Sign up for a free PartHub account</h4>
          </th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td style='text-align:left'>
            
              <label for="inputEmail3" class="form-label">Email</label>
              <input type="email" class="form-control" id="inputEmail3" name="email">
              <label for="inputPassword3" class="form-label">Password</label>
              <input type="password" class="form-control" id="inputPassword3" name="passwd">
          </td>
        </tr>
        <tr>
          <td>
            <script src="https://www.google.com/recaptcha/api.js" async defer></script>

            <button type="submit" class="btn btn-primary">Sign up</button>
          </td>
        </tr>
        <tr>
          <td style="text-align:center">
          <div class="g-recaptcha" data-sitekey="6Lca_UAlAAAAAHLO5OHYaIvpzTPZAhf51cvz3LZE">
            </div>
          </td>
        </tr>

      <tbody>
    </table>
    </form>
  </div>
</div>

<style>
  .g-recaptcha {
    display: inline-block;
  }
</style>