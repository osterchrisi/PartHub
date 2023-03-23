<?php
$basename = basename(__FILE__);
include '../config/credentials.php';
include '../config/show-stock-columns.php';
include 'SQL.php';
include 'forms.php';
include 'get.php';
include 'tables.php';
include 'session.php';

$conn = connectToSQLDB($hostname, $username, $password, $database_name);
$part_id = getSuperGlobal('part_id');

// Get part name
$result = getPartName($conn, $part_id);
$part_name = $result[0]['part_name'];
?>

<!-- HTML for modal -->
<div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
      <h1 class="modal-title fs-5">Add Stock</h1>
      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>

    <div class="modal-body mx-1">
      Add Stock for <?php echo $part_name; ?><br><br>
      <form>
        <input class="form-control stockModalNumber" placeholder="Quantity" id="addStockQuantity"><select><br>
        <input class="form-control" placeholder="Description / PO" id="addStockDescription">
      </form>
    </div>

    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      <button type="button" class="btn btn-primary" data-bs-dismiss="modal" id="AddStock">Save changes</button>
    </div>
  </div>
</div>