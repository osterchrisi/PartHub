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
$results_per_page = getSuperGlobal('resultspp', '50');

$conn = connectToSQLDB($hostname, $username, $password, $database_name);
?>

<!-- BOM List Right-click Menu -->
<div id="bom_list_table_menu" class="dropdown-menu">
  <a class="dropdown-item" href="#" data-action="delete">Delete BOM(s)</a>
  <a class="dropdown-item disabled" href="#" data-action="assignC">Assemble</a>
</div>

<div class="container-fluid">
  <?php require_once('../includes/navbar.php'); ?>
  <br>
  <h4>BOM List</h4>

  <!-- Search Form -->
  <div class="row">
    <div class="col-3">
      <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input class="form-control" type="text" id="search" name="search" placeholder="Search BOMs...">
    </div>
    <div class="col-1">
      <button type="submit" name="submit" class="btn btn-primary">Show Results</button>
    </div>
    <div class="col-1">
      <?php echo "Results per page:"; ?>
    </div>
    <div class="col-1">
      <?php generateResultsDropdown($results_per_page); ?>
    </div>
    <div class="col-1">
      <button type="button" class="btn btn-outline-primary" onclick="displayBomCreate()">Add BOM</button>
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

      echo "<br>Displaying $total_rows search results";

      echo "<div class='row'>";
      echo "<div class='col-6' id='table-window'>"; // Display BOMs
      buildBomListTable($bom_list, $db_columns, $nice_columns, $table_name, $id_field);
      echo "</div>";
      echo "<div class='col-6' id='info-window' style='border:1px solid rgba(0, 255, 255, 0.1)'>"; // Display additional data on BOM
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
    bootstrapBomListTable();

    var $table = $('#bom_list_table');
    var $menu = $('#bom_list_table_menu');
    defineBomListTableActions($table, $menu);
    inlineProcessing();
  </script>