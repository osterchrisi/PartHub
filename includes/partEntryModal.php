<?php
// The part entry modal from the parts inventory
$basename = basename(__FILE__);
include '../config/credentials.php';
// include 'SQL.php';
// include 'forms.php';
// include 'get.php';
// include 'tables.php';
// include 'session.php';

$conn = connectToSQLDB($hostname, $username, $password, $database_name);
// $part_id = getSuperGlobal('part_id');

// // Get part name
// $result = getPartName($conn, $part_id);
// $part_name = $result[0]['part_name'];

// // Get locations
// $locations = getLocations($conn);
// $_SESSION['locations'] = $locations;
?>

<!-- HTML for modal -->
<div class="modal-dialog">
  <div class="modal-content">
    <!-- Modal Header -->
    <div class="modal-header">
      <h1 class="modal-title fs-5" id="partEntryModalTitle">Add New Part</h1>
      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>

    <!-- Modal Body -->
    <div class="modal-body mx-1">
      <span id="partEntryText">Add new part to database</span>
      <?php echo $part_name; ?><br><br>
      <form id="partEntryForm">
        <input class="form-control" placeholder="Part Name" required><br>
        <div class="row">
          <div class="col">
            <input class="form-control" placeholder="Quantity" required>
          </div>
          <div class="col"><input class="form-select" placeholder="Storage Location" required>
          </div>
        </div>
        <br>
        <button class="btn btn-sm" id="showAdvanced" type="button" data-bs-toggle="collapse" data-bs-target="#advancedOptions">Show Advanced</button>
        <div class="collapse" id="advancedOptions">
          <input class="form-control not-required" placeholder="Description"><br>
          <input class="form-control not-required" placeholder="Comment"><br>
          <input class="form-select not-required" placeholder="Footprint">
        </div>
        <div class="input-group"></div>
      </form>
    </div>

    <!-- Modal Footer -->
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      <button type="submit" class="btn btn-primary" id="addPart">Add Part</button>
    </div>
  </div>
</div>