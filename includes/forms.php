<?php
// A bunch of form generating functions
function generateCategoriesDropdown($categories, $sc)
{
  // Generate dropdown menu for the column names
  echo '<select multiple size="10" class="form-select form-select-sm" id="cat-select">';

  // This ternary operator checks if the searched category $sc is set and selects it, if it is the same as the option
  echo '<option value="all" ' . ((!$sc or $sc == "all" OR in_array("all", $sc)) ? "selected" : "") . '>All Categories</option>';

  // Iterate over all available search columns
  foreach ($categories as $category) {
    $selected = '';
    if ($sc && is_array($sc) && in_array($category['category_id'], $sc)) {
      $selected = 'selected';
    }
    echo "<option value='" . $category['category_id'] . "' " . $selected . ">" . $category['category_name'] . "</option>";
  }
  echo '</select>';
}

function generateBackordersDropdown()
{
  // Generate the dropdown menu for the column names. Could be automated with the array that I use for SQL queries

  echo '<select name="search_column" class="form-select">';
  echo '<option value="everywhere">Everywhere</option>';
  echo "<option value='customers.customer_name'>Customers</option>";
  echo "<option value='backorders.customer_po'>PO numbers</option>";
  echo "<option value='products.product_name'>Products</option>";
  echo "<option value='backorder_statuses.status_name'>Status</option>";
  echo '</select>';
}

function generateBackordersStatusDropdown()
{
  // Generate the dropdown for filtering for different backorder statuses

  echo '<select name="search_status" class="form-select">';
  echo '<option value="all">All</option>';
  echo "<option value='1'>Open</option>";
  echo "<option value='2'>Fulfilled</option>";
  echo "<option value='3'>Partially Fulfilled</option>";
  echo '</select>';
}

function generateResultsDropdown($results_per_page)
{
  // Generate dropdown for different results per page options
  $options = [
    "10" => "10",
    "25" => "25",
    "50" => "50",
    "100" => "100"
  ];
  ?>

  <select name="resultspp" id="resultspp" class="form-select form-select-sm" style="width:auto">
    <?php foreach ($options as $value => $name) { ?>
      <option value="<?= $value; ?>" <?= ($value == $results_per_page) ? ' selected' : ''; ?>><?= $name; ?></option>
    <?php } ?>
  </select>
<?php
}

function generateBackordersCustomersDropdown($conn)
{
  // Generate the dropdown menu for the products in the backorders entry
  $stmt = $conn->prepare('SELECT id, customer_name FROM customers');
  $stmt->execute();

  // Fetch all rows into an array
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Generate the dropdown, the value is the id but the name is shown to the user
  echo '<select name="customer_id" class="form-select" label="In:">';
  foreach ($rows as $row) {
    echo "<option value='{$row['id']}'>{$row['customer_name']}</option>";
  }
  echo '</select>';
}

function generateBomNamesDropdown($conn)
{
  // Generate the dropdown menu for the products in the backorders entry
  $stmt = $conn->prepare('SELECT bom_id, bom_name FROM bom_names');
  $stmt->execute();

  // Fetch all rows into an array
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Generate the dropdown, the value is the id but the name is shown to the user
  echo '<select name="customer_id" class="form-select">';
  foreach ($rows as $row) {
    echo "<option value='{$row['id']}'>{$row['bom_name']}</option>";
  }
  echo '</select>';
}