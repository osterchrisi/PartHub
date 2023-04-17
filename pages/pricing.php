<?php
// Pricing page
$basename = basename(__FILE__);
$title = 'Pricing';
require_once('../includes/head.html');

include '../config/credentials.php';
include '../includes/SQL.php';
require_once('../includes/navbar.php'); ?>

<div class="d-flex flex-grow-1 justify-content-center align-items-center">
  <div class="greeting d-flex align-items-center">
    <table class="table table-borderless text-center mx-auto w-auto" style="borders: false">
      <thead>
        <tr>
          <th>
            <h4>PartHub Pricing</h4>
          </th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td style='text-align:left'>
            Currently PartHub is free to use but be aware that it is still in beta.
          </td>
        </tr>
        <tr>
          <td>
            <button type="button" class="btn btn-primary" onclick="window.location.href='signup.php'">Sign
              Up</button>
          </td>
        </tr>
      <tbody>
    </table>
  </div>
</div>