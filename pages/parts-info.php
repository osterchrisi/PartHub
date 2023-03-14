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

<div class="container-fluid">
<br>

<h4><?php echo $part_name;?></h4>
<h5>Total stock: <?php echo $total_stock;?></h5>

<?php buildTable($db_columns, $nice_columns, $result);?>

<div class="btn-group btn-group-sm" role="group">
  <button type="button" class="btn btn-outline-primary">Add</button>
  <button type="button" class="btn btn-outline-primary">Move</button>
  <button type="button" class="btn btn-outline-primary">Reduce</button>
</div>
<br><br>

<h5>Part of:</h5>
<h5>Datasheet:</h5>
<h5>Image:</h5>


</div>