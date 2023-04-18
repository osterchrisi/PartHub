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

// Get BOM name and description
$bom_info = getBomName($conn, $bom_id);
$bom_name = $bom_info[0]['bom_name'];
$bom_description = $bom_info[0]['bom_description'];

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

  <h5>
    <?php echo $bom_description;?>
</h5>

  <!-- BOM Elements Table -->
  <?php
  // print_r(($bom_elements));
  include '../config/bom-details-columns.php';
  buildBomDetailsTable($db_columns, $nice_columns, $bom_elements, $conn); ?>
</div>

<script>
  bootstrapBomDetailsTable();

  // Allow extra HTML elements for the popover mini stock table
  var myDefaultAllowList = bootstrap.Tooltip.Default.allowList

  // Allow table elements
  myDefaultAllowList.table = []
  myDefaultAllowList.thead = []
  myDefaultAllowList.tr = []
  myDefaultAllowList.td = []
  myDefaultAllowList.tbody = []

  // Allow td elements and data-bs-option attributes on td elements
  myDefaultAllowList.td = ['data-bs-option']

  // Initialize all popovers
  popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
  popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));

  // Re-initialize the popovers after toggling a column
  //* This should be possible via the 'column-switch.bs.table' but it never fires...
  $(function () {
    $('#BomDetailsTable').on('post-body.bs.table', function () {
      popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
      popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));
    });
  });
</script>