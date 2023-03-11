<?php
  require_once('../includes/head.html');
  include '../config/credentials.php';
  include '../config/show-bom-columns.php';
  include '../includes/SQL.php';
  include '../includes/forms.php';
  include '../includes/get.php';
  include '../includes/tables.php';

  $conn = connectToSQLDB($hostname, $username, $password, $database_name);
  $bom_id = getSuperGlobal('id');

  // Get BOM name
  $result = getBomName($conn, $bom_id);
  $bom_name = $result[0]['bom_name'];
  
  // Get BOM elements
  $result = getBomElements($conn, $bom_id);  
?>

<div class="container-fluid">
<?php
// Check if called within the info window
if (isset($_GET['hideNavbar']) && $_GET['hideNavbar'] == 'true') {
  // Don't include the navbar
} else {
  require_once('../includes/navbar.php');
}?>
<br>

<h4><?php echo $bom_name;?></h4>

<?php buildTable($db_columns, $nice_columns, $result);?>
</div>