<?php
  require_once('head.html');
  include 'config/credentials.php';
  include 'config/show-stock-columns.php';
  include 'lib/SQL.php';
  include 'lib/forms.php';
  include 'lib/get.php';
  include 'lib/tables.php';

  $conn = connectToSQLDB($hostname, $username, $password, $database_name);
  $part_id = getPartID();

  // Get part name
  $result = getPartName($conn, $part_id);
  $part_name = $result[0]['part_name'];
  
  // Get stock levels
  $result = getStockLevels($conn, $part_id);
  $total_stock = getTotalStock($result);
  
?>

<div class="container-fluid">
<?php require_once('navbar.php');?>
<br>

<h4><?php echo $part_name;?></h4>
<h5>Total stock: <?php echo $total_stock;?></h5>

<?php buildTable($db_columns, $nice_columns, $result, '33%');?>
</div>