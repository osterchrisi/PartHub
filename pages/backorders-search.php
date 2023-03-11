<?php
  $title = 'Search Backorders';
  require_once('../includes/head.html');
  include 'config/credentials.php';
  include '../includes/SQL.php';
  include '../includes/forms.php';
  $table_name = "backorders";
  
  $conn = connectToSQLDB($hostname, $username, $password, $database_name);
?>
<div class="container-fluid">
<?php require_once('../includes/navbar.php');?>
<br>
<h4>Search backorders</h4>

<div class="row">
  <div class="col-3">
    <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <input class="form-control" type="text" id="search" name="search" placeholder="Filter backorders...">
  </div>
  <div class="col-3">
    <?php generateBackordersDropdown(); ?>
    <br>
    <button type="submit" name="submit" class="btn btn-primary">Show Results</button>
  </div>
  <div class="col-3">
    <?php echo "Status: "; generateBackordersStatusDropdown();?>
    <?php echo "Results per page: "; generateResultsDropdown();?>
  </div>
  </form>
</div>

<?php
include '../includes/helpers.php';
include '../includes/get.php';
include '../includes/tables.php';
include '../includes/pagination.php';
include '../config/search-backorder-columns.php';
$results_per_page = getSuperGlobal('resultspp', '50');
$search_status = getSuperGlobal('search_status', 'all');

try {
  $search_column = getSuperGlobal('search_column', 'everywhere');
  $search_term = getSuperGlobal('search');
  // echo "columsn = $column_names";
  $total_rows = getTotalNumberOfBackorderRows($conn, $table_name, $search_column, $search_term, $search_status);
  
  if ($total_rows){
    // Calculate the total number of pages for pagination
    $total_pages = ceil($total_rows / $results_per_page);
    $current_page = getCurrentPageNumber($total_pages);

    // Calculate the offset for the current page
    $offset = ($current_page - 1) * $results_per_page;

    $result = backorder_query($table_name, $search_column, $search_term, $offset, $results_per_page, $conn, $column_names, $search_status);

    echo "<br>Displaying $total_rows search results for \"$search_term\" in \"$table_name\" in \"$search_column\" with $results_per_page results per page:<br><br>";

    buildBackordersTable($result, $db_columns, $nice_columns);
    displayPaginationLinks($total_pages, $current_page);
  }
  else {
    noResults();
  }
}
catch (Exception $e) {
    echo "<br>Error: " . $e->getMessage();
}

// Close the connection
$conn = null;
?>