<?php
include '../config/credentials.php';
include '../config/show-stock-columns.php';
include '../includes/SQL.php';
include '../includes/forms.php';
include '../includes/get.php';
include '../includes/tables.php';

$conn = connectToSQLDB($hostname, $username, $password, $database_name);
$part_id = getSuperGlobal('part_id');

// Get part name
$result = getPartName($conn, $part_id);
$part_name = $result[0]['part_name'];

// Get stock levels
$result = getStockLevels($conn, $part_id);
$total_stock = getTotalStock($result);

?>
<!--#include virtual="../includes/stockModals.html" -->

<div class="container-fluid">
  <br>

  <h4>
    <?php echo $part_name; ?>
  </h4>
  <h5>Total stock:
    <?php echo $total_stock; ?>
  </h5>

  <?php buildTable($db_columns, $nice_columns, $result); 
  include '../includes/stockModals.php';
  ?>

  <div class="input-group">
  <input type="text" class="form-control" placeholder="Stock:" disabled readonly>
    <!-- <div class="btn-group btn-group-sm" role="group" id="stock-buttons"> -->

      <button type="button" class="btn btn-outline-primary"
      data-bs-toggle="modal"
      data-bs-target="#mAddStock"
      data-part-name="<?php echo $part_name; ?>">Add</button>

      <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#mMoveStock">Move</button>

      <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#mReduceStock">Reduce</button>
    <!-- </div> -->
  </div>
  <br><br>

  <h5>Part of:</h5>
  <h5>Datasheet:</h5>
  <h5>Image:</h5>


</div>
