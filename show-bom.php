<?php
  include 'config/credentials.php';
  include 'config/show-bom-columns.php';
  include 'lib/SQL.php';
  include 'lib/forms.php';
  include 'lib/get.php';
  include 'lib/tables.php';

  $conn = connectToSQLDB($hostname, $username, $password, $database_name);
  $bom_id = getBomID();

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
  require_once('navbar.php');
}?>
<br>

<h4><?php echo $bom_name;?></h4>

<?php buildTable($db_columns, $nice_columns, $result);?>
</div>