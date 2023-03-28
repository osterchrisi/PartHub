<?php
// Shows details of selected BOM in info-window
$basename = basename(__FILE__);
include '../config/credentials.php';
include '../config/show-bom-columns.php';
include '../includes/SQL.php';
include '../includes/forms.php';
include '../includes/get.php';
include '../includes/tables.php';

$conn = connectToSQLDB($hostname, $username, $password, $database_name);
$bom_id = getSuperGlobal('id'); // It's the BOM ID

// Get BOM name 
$bom_name = getBomName($conn, $bom_id)[0]['bom_name'];

// Get BOM elements
$bom_elements = getBomElements($conn, $bom_id);
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
    <?php echo $bom_name; ?>
  </h4>

  <!-- BOM Elements Table -->
  <?php
  // print_r(($bom_elements));
  include '../config/bom-details-columns.php';
  buildBomDetailsTable($db_columns, $nice_columns, $bom_elements, $conn); ?>
</div>

<div id="inline-stock">asdasdf</div>

<script>
  bootstrapBomDetailsTable();

  var myDefaultAllowList = bootstrap.Tooltip.Default.allowList

  // To allow table elements
  myDefaultAllowList.table = []
  myDefaultAllowList.thead = []
  myDefaultAllowList.tr = []
  myDefaultAllowList.td = []
  myDefaultAllowList.tbody = []

  // To allow td elements and data-bs-option attributes on td elements
  myDefaultAllowList.td = ['data-bs-option']

  const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]')
  const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl))

  // Supposedly need the below code for being dismissale but it seems dismissable anyway (at least in my Chrome)
  // popover = new bootstrap.Popover('.popover-dismiss', {
  //   container: 'inline-stock'
  //   //trigger: 'focus'
  // })
</script>