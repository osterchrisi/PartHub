<?php
// Parts Inventory Page
$basename = basename(__FILE__);
$title = 'Parts Inventory';
require_once('../includes/head.html');

include '../config/credentials.php';
include '../includes/SQL.php';
include '../includes/forms.php';
include '../includes/get.php';

$table_name = "parts";
$cat = $_GET['cat'][0]; // Get the JSON-encoded string from the form data

$cat = json_decode($cat); // Convert the string to a PHP array

// $cat is now an array of nested arrays, so we need to extract the category IDs
$cat_ids = array();
foreach ($cat as $cat_array) {
  $cat_ids[] = $cat_array[0];
}

// $search_category = $cat_ids;
if (empty($cat_ids)){
  $cat_ids[0] = 'all';
}
$_GET['cat'] = $cat_ids;


$search_term = getSuperGlobal('search');
$search_category = getSuperGlobal('cat', ['all']);


$conn = connectToSQLDB($hostname, $username, $password, $database_name);
$column_names = getColumnNames($conn, $table_name);
$results_per_page = getSuperGlobal('resultspp', '50');

$categories = getCategories($conn);
// print_r($categories);
?>

<!-- Stock Modal - gets dynamically updated via JS -->
<div class="modal fade" id="mAddStock" tabindex="-1">
</div>

<!-- Page Contents -->
<div class="container-fluid">
  <?php require_once('../includes/navbar.php'); ?>
  <br>
  <h4>Parts Inventory</h4>

  <!-- Search form -->
  <div class="row">
    <div class="col-3">
      <form method="get" id="search_form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input type="text" class="form-control" id="search" name="search" placeholder="Search parts..."
          value="<?php echo htmlspecialchars($search_term); ?>"><br><br><br>
        <input type="text" class="form-control" id="filter" placeholder="Filter results on this page...">
    </div>
    <div class="col-3">
      <!-- <input class="form-control" placeholder="Search categories" id="categories-filter"> -->
      <input type="hidden" name="cat[]" id="selected-categories" value="">

      <?php
      //TODO: Button to reset filters
      generateCategoriesDropdown($categories, $search_category); ?>
    </div>
    <div class="col-1">
      <button type="submit" class="btn btn-primary" name="apply">Search</button><br><br>
      <button class="btn btn-primary" name="AddNew" id="AddNew" type="button">New Entry</button>
    </div>
    <div class="col-1">
      <?php echo "Results per page:"; ?>
    </div>
    <div class="col-1">
      <div class="row me-1 justify-content-end">
        <?php generateResultsDropdown($results_per_page); ?>
      </div>
      <br>
    </div>
    </form>
  </div>

  <!-- Parts Table and Pagination -->
  <?php
  include '../includes/helpers.php';
  include '../includes/tables.php';
  include '../includes/pagination.php';
  include '../config/inventory-columns.php';
  $results_per_page = getSuperGlobal('resultspp', '50');

  try {
    //* Essentially setting search column to everywhere because currently not in use in the search form
    $search_column = getSuperGlobal('search_column', 'everywhere');
    // The term from the search field
    $search_term = getSuperGlobal('search');
    //Get the number of results for pagination
    $total_rows = getTotalNumberOfRows($conn, $table_name, $search_column, $search_term, $column_names, $search_category);

    if ($total_rows) {
      // Calculate the total number of pages for pagination
      $total_pages = ceil($total_rows / $results_per_page);
      $current_page = getCurrentPageNumber($total_pages);

      // Calculate the offset for the current page
      $offset = ($current_page - 1) * $results_per_page;

      $result = queryDB($table_name, $search_column, $search_term, $offset, $results_per_page, $conn, $column_names, $search_category);

      echo "<div class='row'>";
      echo "<div class='col-9' id='table-window' style='max-width: 90%;'>"; //9
      // Display parts across a 9-column
      buildPartsTable($result, $db_columns, $nice_columns, $total_stock, $conn, $table_name);
      echo "</div>";
      echo "<div class='col d-flex h-50 sticky-top resizable justify-content-center info-window' id='info-window'>"; // height:75vh'>";
      // Display additional info on part in 3-column
      echo "<h6><br>Click on a row in the table</h6>";
      echo "</div>";
      echo "</div>";

      // Pagnination links
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

</div>

<script>
  bootstrapPartsTable();

//   $(function () {
//   var $select = $('#cat-select').selectize();

//   $('form').on('submit', function () {
//     // Get the selected options from the Selectize instance
//     var selectedValues = $select[0].selectize.getValue();

//     // Convert the array of selected values to an array of strings
//     var selectedIDs = [];
//     for (var i = 0; i < selectedValues.length; i++) {
//       selectedIDs.push(selectedValues[i]);
//     }

//     // Convert the array of selected IDs to a comma-separated string
//     var selectedIDsString = selectedIDs.join(',');

//     // Update the value of the hidden input with the selected IDs string
//     $('#selected-categories').val(selectedIDsString);

//     return true; // Allow the form to be submitted
//   });
// });

  $(function () {
    var $select = $('#cat-select').selectize();

    $('form').on('submit', function () {
      // Get the selected options from the Selectize instance
      var selectedValues = $select[0].selectize.getValue();
      console.log(selectedValues);
      console.log(selectedValues[2]);
      a = selectedValues;
      for (var i = 0; i < a.length; i++) {
        a[i] = [a[i]];
      }
      console.log(a);
      b = JSON.stringify(a);
      console.log(b);
      // selectedValues = JSON.stringify(selectedValues);

      // Update the value of the original <select> element
      $('#selected-categories').val(b);

      return true; // Allow the form to be submitted
    });
  });

  // Get part_id from the clicked row and update parts-info and stock modals
  $(document).ready(function () {
    $('#parts_table tbody').on('click', 'tr', function () {
      console.log("Row clicked");
      if ($('tbody tr.selected').length > 0) {
        $('tbody tr.selected').removeClass('selected');
      }
      $(this).toggleClass('selected');
      var id = $(this).data('id'); // get ID from the selected row
      updatePartsInfo(id);
      updateStockModal(id);
    });

    // Focus the Quantity field in the stock changes modal after showing
    $('#mAddStock').on('shown.bs.modal', function () {
      console.log("Modal now ready");
      $('#addStockQuantity').focus();
    });
  });

</script>