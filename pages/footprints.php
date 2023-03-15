<?php
$title = 'Footprints';
require_once('../includes/head.html');
include '../config/credentials.php';
include '../includes/inline-processing.php';
include '../includes/SQL.php';
include '../includes/forms.php';
$table_name = "part_categories";

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
    <h4>Footprints</h4>

    <div class="input-group mb-3">
  <input type="text" class="form-control" placeholder="Search for resistors...">
  <select multiple class="form-select">
    <option value="1">Option 1</option>
    <option value="2">Option 2</option>
    <option value="3">Option 3</option>
    <option value="4">Option 4</option>
    <option value="5">Option 5</option>
    <!-- more options here... -->
  </select>
</div>


<div class="input-group mb-3">
  <div class="row">
    <div class="col">
      <input type="text" class="form-control" placeholder="Search for resistors...">
    </div>
  </div>
  <div class="row">
    <div class="col">
      <select multiple class="form-select">
        <option value="1">Option 1</option>
        <option value="2">Option 2</option>
        <option value="3">Option 3</option>
        <option value="4">Option 4</option>
        <option value="5">Option 5</option>
        <!-- more options here... -->
      </select>
    </div>
  </div>
</div>

<div class="input-group mb-3">
  <!-- <div class="input-group-prepend">
    <span class="input-group-text"><i class="bi bi-search"></i></span>
  </div> -->
  <div class="position-relative">
    <input type="text" class="form-control" placeholder="Filter categories">
    <select multiple class="form-select position-absolute top-100 start-0">
      <option value="1">Option 1</option>
      <option value="2">Option 2</option>
      <option value="3">Option 3</option>
      <option value="4">Option 4</option>
      <option value="5">Option 5</option>
      <!-- more options here... -->
    </select>
  </div>
</div>

<style>
    .input-group > .form-control {
  width: calc(100% - 38px);
  padding-right: 0;
}

.position-relative {
  position: relative;
}

.position-absolute.top-100.start-0 {
  top: 100%;
  start: 0;
  width: 100%;
}
</style>