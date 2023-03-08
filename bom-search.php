<?php
$title = 'Show BOM';
require_once('head.html');
include 'config/credentials.php';
include 'lib/SQL.php';
include 'lib/forms.php';
$table_name = "bom_names";

$conn = connectToSQLDB($hostname, $username, $password, $database_name);
?>
<div class="container-fluid">
  <?php require_once('navbar.php'); ?>
  <br>
  <h4>Search BOMs</h4>

  <!-- Search Form -->
  <div class="row">
    <div class="col-3">
      <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input class="form-control" type="text" id="search" name="search" placeholder="Filter BOMs...">
    </div>
    <div class="col-1">
      <button type="submit" name="submit" class="btn btn-primary">Show Results</button>
    </div>
    <div class="col-1">
      <?php echo "Results per page:"; ?>
    </div>
    <div class="col-5">
      <?php generateResultsDropdown(); ?>
    </div>
    </form>
  </div>

  <?php
  include 'lib/helpers.php';
  include 'lib/get.php';
  include 'lib/tables.php';
  include 'lib/pagination.php';
  include 'config/search-bom-columns.php';
  $results_per_page = getResultsPerPage();

  try {
    $search_term = getSearchTerm();
    $total_rows = getTotalNumberOfBomRows($conn, $table_name, $search_term);

    if ($total_rows) {
      // Calculate the total number of pages for pagination
      $total_pages = ceil($total_rows / $results_per_page);
      $current_page = getCurrentPageNumber($total_pages);

      // Calculate the offset for the current page
      $offset = ($current_page - 1) * $results_per_page;

      $result = bom_query($conn, $table_name, $search_term, $offset, $results_per_page);

      echo "<br>Displaying $total_rows search results";

      echo "<div class='row'>";
      echo "<div class='col-6'>"; // Display BOMs
      buildBomTable($result, $db_columns, $nice_columns);
      echo "</div>";
      echo "<div class='col-6' id='info-window' style='border:1px solid rgba(0, 255, 255, 0.1)'>"; // Display additional data on BOM
      echo "</div>";
      echo "</div>";
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

  <script>
    // Get BOM ID from the clicked row and pass it to show-bom.php for showing details in the info-window
    $(document).ready(function () {
      $('tr').click(function () {
        $('tbody tr').removeClass('selected');
        $(this).toggleClass('selected');
        var id = $(this).data('id'); // get the ID from the first cell of the selected row
        var bom_name = $(this).find('td:nth-child(2)').text(); // Don't use it currently actually

        // Load the PHP page and pass the id variable as a parameter
        $.ajax({
          url: 'show-bom.php',
          type: 'GET',
          data: { id: id, hideNavbar: true },
          success: function (data) {
            // Replace the content of the info window with the loaded PHP page
            $('#info-window').html(data);
          },
          error: function () {
            // Display an error message if the PHP page failed to load
            $('#info-window').html('Failed to load additional data.');
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