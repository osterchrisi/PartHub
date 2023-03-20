<?php
$title = 'Parts Inventory';
require_once('../includes/head.html');

include '../config/credentials.php';
include '../includes/SQL.php';
include '../includes/forms.php';
include '../includes/get.php';

$table_name = "parts";

$search_term = getSuperGlobal('search');
$search_category = getSuperGlobal('cat', 'all');

$conn = connectToSQLDB($hostname, $username, $password, $database_name);
$column_names = getColumnNames($conn, $table_name);
$results_per_page = getSuperGlobal('resultspp', '50');

?>

<div class="container-fluid">
  <?php require_once('../includes/navbar.php'); ?>
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
      <input class="form-control" placeholder="Search categories" id="categories-filter">
      <?php
      $categories = getCategories($conn);

      // echo "<pre>";
      // print_r($categories);
      // echo "end ";
      generateCategoriesDropdown($categories, $search_category); ?>
    </div>
    <div class="col-1">
      <button type="submit" class="btn btn-primary" name="apply">Apply Filters</button><br><br>
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


  <?php
  include '../includes/helpers.php';
  include '../includes/tables.php';
  include '../includes/pagination.php';
  include '../config/inventory-columns.php';
  $results_per_page = getSuperGlobal('resultspp', '50');

  try {
    $search_column = getSuperGlobal('search_column', 'everywhere');
    $search_term = getSuperGlobal('search');
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
      echo "<div class='col-9' id='table-window' style='max-width: 90%;'>"; //9
      // Display parts across a 9-column
      buildPartsTable($result, $db_columns, $nice_columns, $total_stock, $conn, $table_name);
      echo "</div>";
      echo "<div class='col d-flex resizable justify-content-center' id='info-window' style='border:1px solid rgba(0, 255, 255, 0.1);overflow: auto;'>"; // height:75vh'>";
      // Display additional info on part in 3-column
      echo "<h6><br>Click on a part in the table</h6>";
      echo "</div>";
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
  // Get part_id from the clicked row and pass it to show-stock.php for showing details in the info-window
  $(document).ready(function () {
    $('tr').click(function () {
      $('tbody tr').removeClass('selected');
      $(this).toggleClass('selected');
      var id = $(this).data('id'); // get the ID from the first cell of the selected row
      // var part_name = $(this).find('td:nth-child(2)').text(); // Currently not in use...
      // console.log("part_name: ", part_name);

      // Load the PHP page and pass the id variable as a parameter
      $.ajax({
        url: 'parts-info.php',
        type: 'GET',
        data: { part_id: id, hideNavbar: true },
        success: function (data) {
          // Replace the content of the info window with the loaded PHP page
          $('#info-window').html(data);
        },
        error: function () {
          // Display an error message if the PHP page failed to load
          $('#info-window').html('Failed to load additional part data.');
        }
      });
    });
  });
</script>

<style>
  tr.selected {
    background-color: rgba(0, 255, 255, 0.1);
  }
</style>


<!-- Resizable Divs -->
<script>
  $(function () {
    $('#table-window').resizable({
      handles: 'e',
      resize: function() {
        var parentWidth = $('#table-window').parent().width();
        var tableWidth = $('#table-window').width();
        var infoWidth = parentWidth - tableWidth;
        $('#info-window').width(infoWidth);
      }
    });
  });
</script>

<!-- Filter categories -->
<script>
$(document).ready(function() {
  $('#categories-filter').on('input', function() {
    var filterText = $(this).val().toLowerCase();
    $('#cat-select option').each(function() {
      var optionText = $(this).text().toLowerCase();
      if (optionText.indexOf(filterText) !== -1) {
        $(this).show();
      } else {
        $(this).hide();
      }
    });
  });
});
</script>

<style>
.ui-resizable-e {
  width: 10px;
  height: 100%;
  position: absolute;
  top: 0;
  right: 0;
  cursor: col-resize;
  z-index: 9999;
}

.ui-resizable-e:before,
.ui-resizable-e:after {
  content: "";
  display: block;
  position: absolute;
  top: 50%;
  left: 50%;
  background-color: #000;
}

.ui-resizable-e:before {
  height: 20px;
  width: 1px;
  /* margin-top: -3px; */
  margin-left: -2px;
  background-color: #F8F8FF;
}

.ui-resizable-e:after {
  height: 20px;
  width: 1px;
  /* margin-top: -4px; */
  /* margin-left: -1px; */
  background-color: #F8F8FF;
}

</style>