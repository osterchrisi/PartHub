<?php
// Parts Inventory Page
$basename = basename(__FILE__);
$title = 'Parts Inventory';
require_once('../includes/head.html');

include '../config/credentials.php';
include '../includes/SQL.php';
include '../includes/forms.php';
include '../includes/get.php';
include '../includes/helpers.php';

$table_name = "parts";
dealWithCats();

$search_term = getSuperGlobal('search');
$search_category = getSuperGlobal('cat', ['all']);


$conn = connectToSQLDB($hostname, $username, $password, $database_name);
$column_names = getColumnNames($conn, $table_name);
$results_per_page = getSuperGlobal('resultspp', '50');

$categories = getCategories($conn);
?>

<!-- Stock Modal -->
<div class="modal fade" id="mAddStock" tabindex="-1"></div>

<!-- Part Entry Modal -->
<div class="modal fade modal-draggable" id="mPartEntry" tabindex="-1">
  <?php include '../includes/partEntryModal.php'; ?>
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
      <input type="hidden" name="cat[]" id="selected-categories" value="">

      <?php
      //TODO: Button to reset filters maybe?
      generateCategoriesDropdown($categories, $search_category); ?>
    </div>
    <div class="col-1">
      <button type="submit" class="btn btn-primary" name="apply">Search</button><br><br>
      <button type="button" class="btn btn-outline-primary" onclick='callPartEntryModal();'>Enter New</button>
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

  <div id="myMenu" class="dropdown-menu">
  <a class="dropdown-item" href="#">Action 1</a>
  <a class="dropdown-item" href="#">Action 2</a>
  <a class="dropdown-item" href="#">Action 3</a>
</div>

  <!-- Parts Table and Pagination -->
  <?php
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
    $total_rows = getTotalNumberOfRows($conn, $table_name, $search_column, $search_term, $column_names, $search_category, $user_id);

    if ($total_rows) {
      // Calculate the total number of pages for pagination
      $total_pages = ceil($total_rows / $results_per_page);
      $current_page = getCurrentPageNumber($total_pages);

      // Calculate the offset for the current page
      $offset = ($current_page - 1) * $results_per_page;

      $result = queryDB($table_name, $search_column, $search_term, $offset, $results_per_page, $conn, $column_names, $search_category, $user_id);

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

  //* 'Selectize' the category multi select, prepare values and append to the hidden input field
  $(function () {
    var $select = $('#cat-select').selectize({
      plugins: ["remove_button", "clear_button"]
    });

    $('form').on('submit', function () {
      // Get the selected options from the Selectize instance
      var selectedValues = $select[0].selectize.getValue();

      // Prepare values to look like an array
      for (var i = 0; i < selectedValues.length; i++) {
        selectedValues[i] = [selectedValues[i]];
      }
      selectedValues = JSON.stringify(selectedValues);

      // Update the value of the hidden input element
      $('#selected-categories').val(selectedValues);
    });
  });

  // Get part_id from the clicked row and update parts-info and stock modals
  //* Update: Removed the class toggling because BT does exactly the same and currently use its functionality
  $(document).ready(function () {
    $('#parts_table tbody').on('click', 'tr', function () {
      if ($('tbody tr.selected-last').length > 0) {
        $('tbody tr.selected-last').removeClass('selected-last');
      }
      $(this).toggleClass('selected-last');
      var id = $(this).data('id'); // get ID from the selected row
      updatePartsInfo(id);
      updateStockModal(id);
    });

    // Focus the Quantity field in the stock changes modal after showing
    $('#mAddStock').on('shown.bs.modal', function () {
      console.log("Modal now ready");
      $('#addStockQuantity').focus();
    });

    // Focus the Part Name field in the part entry modal after showing
    $('#mPartEntry').on('shown.bs.modal', function () {
      console.log("Modal now ready");
      $('#addPartName').focus();
    });

    // Prohibit text selection when pressing shift (for selecting multiple rows)
    var table = document.getElementById("parts_table");

    // Shift is pressed
    document.addEventListener("keydown", function (event) {
      if (event.shiftKey) {
        table.classList.add("table-no-select");
      }
    });

    // Shift is released
    document.addEventListener("keyup", function (event) {
      if (!event.shiftKey) {
        table.classList.remove("table-no-select");
      }
    });

    // get a reference to the table element and the custom menu
    var $table = $('#parts_table');
    var $menu = $('#myMenu');

    // add an event listener for the right-click event on table cells
    $table.on('contextmenu', 'td', function(event) {
      // check if the right mouse button was clicked
      if (event.which === 3) {
        // prevent the default context menu from showing up
        event.preventDefault();

        // get the selected table rows
        var selectedRows = $table.bootstrapTable('getSelections');
        console.log(selectedRows);

        // show the custom menu at the mouse position
        $menu.css({
          left: event.pageX + 'px',
          top: event.pageY + 'px',
          display: 'block'
        });

        // add event listeners for the menu items
        $menu.find('.dropdown-item').off('click').on('click', function () {
          // get the action to perform from the menu item's data attribute
          var action = $(this).data('action');

          // perform the selected action on the selected rows
          switch (action) {
            case 'delete':
              deleteSelectedRows(selectedRows);
              break;
            case 'edit':
              editSelectedRows(selectedRows);
              break;
            case 'copy':
              copySelectedRows(selectedRows);
              break;
            default:
            // do nothing
          }

          // hide the custom menu
          $menu.hide();
        });
      }
    });

    // add an event listener for clicks outside the menu to hide it
    $(document).on('click', function (event) {
      if (!$menu.is(event.target) && $menu.has(event.target).length === 0) {
        $menu.hide();
      }
    });

    // function to delete selected rows
    function deleteSelectedRows(rows) {
      // implementation goes here
    }

    // function to edit selected rows
    function editSelectedRows(rows) {
      // implementation goes here
    }

    // function to copy selected rows
    function copySelectedRows(rows) {
      // implementation goes here
    }


  });


  // Part Entry Modal JS
  <?php include '../assets/js/partEntry.js'; ?>
</script>