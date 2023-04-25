<?php
// BOM List Page
$basename = basename(__FILE__);
$title = 'BOM List';
require_once('../includes/head.html');

include '../config/credentials.php';
include '../includes/SQL.php';
include '../includes/forms.php';
include '../includes/get.php';

$table_name = "bom_names";
$id_field = "bom_id";

$search_term = getSuperGlobal('search');
$results_per_page = getSuperGlobal('resultspp', '50');

$conn = connectToSQLDB($hostname, $username, $password, $database_name);

// Get available locations
$locations = getLocations($conn, $user_id);
?>

<!-- BOM List Right-click Menu -->
<div id="bom_list_table_menu" class="dropdown-menu">
  <a class="dropdown-item" href="#" data-action="delete">Delete BOM(s)</a>
  <a class="dropdown-item" href="#" data-action="assemble">Assemble</a>
</div>

<!-- BOM Assembly Modal -->
<div class="modal fade" id="mBomAssembly" tabindex="-1">
  <?php include '../includes/bomAssemblyModal.php'; ?>
</div>

<div class="container-fluid">
  <?php require_once('../includes/navbar.php'); ?>
  <br>
  <h4>BOM List</h4>

  <!-- Search Form -->
  <div class="row mb-3">
    <div class="col-3">
      <form method="get" id="search_form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input class="form-control form-control-sm" type="text" id="search" name="search" placeholder="Search BOMs..." value="<?php echo htmlspecialchars($search_term); ?>">
    </div>
    <div class="col-1">
      <button type="submit" class="btn btn-sm btn-primary">Show Results</button>
    </div>
    <div class="col-1">
      <?php echo "Results per page:"; ?>
    </div>
    <div class="col-1">
      <?php generateResultsDropdown($results_per_page); ?>
    </div>
    <div class="col-1 px-0">
      <button type="button" class="btn btn-sm btn-outline-primary" onclick="displayBomCreate()">Add BOM</button>
    </div>
    </form>
  </div>

  <?php
  include '../includes/helpers.php';
  include '../includes/tables.php';
  include '../includes/pagination.php';
  include '../config/bom-list-columns.php';

  try {
    $search_term = getSuperGlobal('search');
    $total_rows = getTotalNumberOfBomRows($conn, $table_name, $search_term, $user_id);

    if ($total_rows) {
      // Calculate the total number of pages for pagination
      $total_pages = ceil($total_rows / $results_per_page);
      $current_page = getCurrentPageNumber($total_pages);

      // Calculate the offset for the current page
      $offset = ($current_page - 1) * $results_per_page;

      $bom_list = bom_query($conn, $table_name, $search_term, $offset, $results_per_page, $user_id);

      // echo "<br>Displaying $total_rows search results";
  
      echo "<div class='row'>";
      echo "<div class='col-6' id='table-window' style='max-width: 90%;'>"; // Display BOMs
      buildBomListTable($bom_list, $db_columns, $nice_columns, $table_name, $id_field);
      echo "</div>";
      echo "<div class='col d-flex h-50 sticky-top resizable justify-content-center info-window pb-3' id='info-window' style='max-width: 90%;'>"; // Display additional data on BOM
      echo "<h6><br>Click on a row in the table</h6>";
      echo "</div>";
      echo "</div>";
      displayPaginationLinks($total_pages, $current_page);
    }
    else {
      noResults();
    }
  } catch (Exception $e) {
    echo "<br>Error: " . $e->getMessage();
  }

  // Close the connection
  $conn = null;
  ?>

  <script>
    <?php include '../assets/js/stockChanges.js'; ?>
    $(document).ready(function () {
      bootstrapBomListTable();

      var $table = $('#bom_list_table');
      var $menu = $('#bom_list_table_menu');
      defineBomListTableActions($table, $menu);
      inlineProcessing();
      fromStockLocationDropdown('bomAssembleLocationDiv', <?php echo json_encode($locations); ?>);
      sendFormOnDropdownChange();
    });
  </script>