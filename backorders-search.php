<?php
  require_once('head.html');
  include 'config/credentials.php';
  include 'lib/SQL.php';
  include 'lib/forms.php';
  $table_name = "backorders";
  
  $conn = connectToSQLDB($hostname, $username, $password, $database_name);
?>
<div class="container-fluid">
<?php require_once('navbar.php');?>
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
include 'lib/helpers.php';
include 'lib/get.php';
include 'lib/tables.php';
include 'lib/pagination.php';
include 'config/search-backorder-columns.php';
$results_per_page = getResultsPerPage();
$search_status = getSearchStatus();

try {
  $search_column = getSearchColumn();
  $search_term = getSearchTerm();
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