<?php
$basename = basename(__FILE__);
include '../includes/session.php';
include '../config/credentials.php';
include '../config/show-stock-columns.php';
include '../includes/SQL.php';
include '../includes/forms.php';
include '../includes/get.php';
include '../includes/tables.php';

// Connect to database
$conn = connectToSQLDB($hostname, $username, $password, $database_name);
$part_id = getSuperGlobal('part_id');

// Get part name
$result = getPartName($conn, $part_id);
$part_name = $result[0]['part_name'];

// Get stock levels
$stock_levels = getStockLevels($conn, $part_id);
$total_stock = getTotalStock($stock_levels);

?>

<div class="container-fluid">
  <br>

  <h4>
    <?php echo $part_name; ?>
  </h4>
  <h5>Total stock:
    <?php echo $total_stock; ?>
  </h5>

  <!-- Location / Quantity Table -->
  <?php buildTable($db_columns, $nice_columns, $stock_levels); ?>

  <!-- Stock movement buttons -->
  <div class="input-group">
    <input type="text" class="form-control" placeholder="Stock:" disabled readonly>

    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#mAddStock"
      data-part-name="<?php echo $part_name; ?>">Add</button>

    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
      data-bs-target="#mMoveStock">Move</button>

    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
      data-bs-target="#mReduceStock">Reduce</button>

  </div>
  <br><br>

  <h5>Part of:</h5>
  <h5>Datasheet:</h5>
  <h5>Image:</h5>


</div>

<!-- Click listeners for buttons on stock changing modals -->
<script>
  <?php include '../assets/js/stockChanges.js'; ?>
</script>