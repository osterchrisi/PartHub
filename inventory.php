<?php
  $title = 'Parts';
  require_once('head.html');
  include 'config/credentials.php'; 
  include 'inline-processing.php';
  include 'lib/SQL.php';
  include 'lib/forms.php';
  $table_name = "parts";
  
  $search_term = isset($_GET['search']) ? $_GET['search'] : '';
  $search_column = isset($_GET['search_column']) ? $_GET['search_column'] : '';
  $conn = connectToSQLDB($hostname, $username, $password, $database_name);
  $column_names = getColumnNames($conn, $table_name);
?>
<div class="container-fluid">
<?php require_once('navbar.php');?>
<br>
<h4>Search Parts</h4>

<!-- Search form -->
<div class="row">
  <div class="col-3">
    <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <input type="text" class="form-control" id="search" name="search" placeholder="Search..." value="<?php echo htmlspecialchars($search_term); ?>">
  </div>
  <div class="col-3">
    <?php generateDropdown($column_names, $search_column); ?>
  </div>
  <div class="col-3">
    <button type="submit" class="btn btn-primary" name="submit">Show Results</button>
  </div>
  </form>
</div>

<?php
include 'lib/helpers.php';
include 'lib/get.php';
include 'lib/tables.php';
include 'lib/pagination.php';
include 'config/inventory-columns.php';
$results_per_page = 10;

try {
  $search_column = getSearchColumn();
  $search_term = getSearchTerm();
  $total_rows = getTotalNumberOfRows($conn, $table_name, $search_column, $search_term, $column_names);

  if ($total_rows) {
    // Calculate the total number of pages for pagination
    $total_pages = ceil($total_rows / $results_per_page);
    $current_page = getCurrentPageNumber($total_pages);

    // Calculate the offset for the current page
    $offset = ($current_page - 1) * $results_per_page;

    $result = queryDB($table_name, $search_column, $search_term, $offset, $results_per_page, $conn, $column_names);

    // echo "<pre>";
    // var_dump($result);
    // echo "</pre>";

    buildPartsTable($result, $db_columns, $nice_columns, $total_stock, $conn, $table_name);
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

</div>