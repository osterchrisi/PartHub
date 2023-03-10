<?php 
$title = 'Show Backorders';
require_once('../includes/head.html');?>
<div class="container-fluid">
<?php require_once('../includes/navbar.php');?>
<br>

<?php
  include '../config/credentials.php';
  include '../config/show-backorder-columns.php';
  include '../includes/SQL.php';
  include '../includes/forms.php';
  include '../includes/get.php';
  include '../includes/tables.php';
  
  $table_name = 'backorders';
  $conn = connectToSQLDB($hostname, $username, $password, $database_name);
  $po_number = getPONumber();
  $search_column = 'customer_po';
  $search_term = $po_number;
  $offset = 0;
  $results_per_page = 100;
  $search_status = 'all';

  try {
    $result = show_backorder_query($table_name, $search_column, $search_term, $offset, $results_per_page, $conn, $column_names, $search_status);
  }
  catch (Exception $e) {
    echo "<br>Error: " . $e->getMessage();
  }
 
  $customer = $result[0]['customer_name'];
  $create_date = $result[0]['created_at'];

  echo "<h4>Backorder ".$po_number."</h4>";
  echo "<h5>From $customer</h5>";
  echo "<h6>Date: $create_date</h6>";

  $width = '33%';
  // Actually should use another one here
  buildBackordersTable($result, $db_columns, $nice_columns, $width);
?>

</div>