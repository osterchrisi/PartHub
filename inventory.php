<?php
$title = 'Parts Inventory';
require_once('head.html');
include 'config/credentials.php';
include 'lib/SQL.php';
include 'lib/forms.php';
include 'lib/get.php';
$table_name = "parts";

$search_term = isset($_GET['search']) ? $_GET['search'] : '';
$search_column = isset($_GET['search_column']) ? $_GET['search_column'] : '';
$conn = connectToSQLDB($hostname, $username, $password, $database_name);
$column_names = getColumnNames($conn, $table_name);
$results_per_page = getResultsPerPage();
?>



<div class="container-fluid">
  <?php require_once('navbar.php'); ?>
  <br>
  <h4>Parts Inventory</h4>

  <!-- Search form -->
  <div class="row">
    <div class="col-3">
      <form method="get" id="search_form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input type="text" class="form-control" id="search" name="search" placeholder="Filter results..."
          value="<?php echo htmlspecialchars($search_term); ?>">
    </div>
    <div class="col-3">
      <!-- <?php generateDropdown($column_names, $search_column); ?> -->
    </div>
    <div class="col-1">
      <!-- <button type="submit" class="btn btn-primary" name="submit">Show Results</button><br><br> -->
    </div>
    <div class="col-1">
      <?php echo "Results per page:"; ?>
    </div>
    <div class="col-1">
      <div class="row justify-content-end">
        <?php generateResultsDropdown($results_per_page); ?>
      </div>
      <br>
    </div>
    </form>
  </div>


  <?php
  include 'lib/helpers.php';
  include 'lib/tables.php';
  include 'lib/pagination.php';
  include 'config/inventory-columns.php';
  $results_per_page = getResultsPerPage();

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
  
      echo "<div class='row'>";
      echo "<div class='col-9 g-0'>";
      // Display parts across a 9-column
      buildPartsTable($result, $db_columns, $nice_columns, $total_stock, $conn, $table_name);
      echo "</div>";
      echo "<div class='col-3' id='info-window' style='border:1px solid rgba(0, 255, 255, 0.1); height:75vh'>";
      // Display additional info on part in 3-column
      echo "Info";
      echo "</div>";

      // Pagnination links
      displayPaginationLinks($total_pages, $current_page);
    } else {
      noResults();
    }
  } catch (Exception $e) {
    echo "<br>Error: " . $e->getMessage();
  }

  // Close the connection
  $conn = null;
  ?>

</div>

<script>
  $(function () {
    $('#parts_table').bootstrapTable({
    })
  })
</script>

<script>
  function NumberURLSorter(a, b) {
    return $(a).text() - $(b).text()
  }
</script>

<script>
  // Send form upon changing the results per page dropdown
  var dropdown = document.getElementById("resultspp");

  dropdown.addEventListener("change", function () {
    var form = document.getElementById("search_form");
    form.submit();
  });
</script>

<?php
include 'inline-processing.php';
?>