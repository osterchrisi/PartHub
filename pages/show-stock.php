<?php
// Creates a simple page with a 'Location', 'Quantity' table
$basename = basename(__FILE__);
require_once('../includes/head.html');
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
  <?php
  // Check if called within the info window
  if (isset($_GET['hideNavbar']) && $_GET['hideNavbar'] == 'true') {
    // Don't include the navbar
  }
  else {
    require_once('../includes/navbar.php');
  } ?>
  <br>

  <h4>
    <?php echo $part_name; ?>
  </h4>
  <h5>Total stock:
    <?php echo $total_stock; ?>
  </h5>

  <?php buildTable($db_columns, $nice_columns, $result); ?>
</div>