<?php
$basename = basename(__FILE__);
include '../includes/session.php';
include '../config/credentials.php';
include '../config/show-stock-columns.php';
include '../includes/SQL.php';
include '../includes/forms.php';
include '../includes/get.php';
include '../includes/tables.php';

// Connect to database
$conn = connectToSQLDB($hostname, $username, $password, $database_name);
$part_id = getSuperGlobal('part_id');

// Get part name
$result = getPartName($conn, $part_id);
$part_name = $result[0]['part_name'];

// Get stock levels
$stock_levels = getStockLevels($conn, $part_id);
$total_stock = getTotalStock($stock_levels);

$_SESSION['stock_levels'] = $stock_levels;

// Get locations
$locations = getLocations($conn);
?>

<div class="container-fluid">
  <br>
  <h4>
    <?php echo $part_name; ?>
  </h4>
  <h5>Total stock:
    <?php echo $total_stock; ?>
  </h5>

  <!-- Parts Tabs -->
  <ul class="nav nav-tabs" id="partsTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="partInfoTab" data-bs-toggle="tab" data-bs-target="#partStockInfo"
        type="button" role="tab">Info</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="partStockHistoryTab" data-bs-toggle="tab" data-bs-target="#partStockHistory"
        type="button" role="tab">Stock History</button>
    </li>
  </ul>

  <!-- Tabs Content -->
  <div class="tab-content" id="partsTabsContent">
    <div class="tab-pane fade show active" id="partStockInfo" role="tabpanel" tabindex="0">
      <br>
      <!-- Location / Quantity Table -->
      <?php buildTable($db_columns, $nice_columns, $stock_levels); ?>

      <!-- Stock movement buttons -->
      <div class="input-group">
        <input type="text" class="form-control" placeholder="Stock:" disabled readonly>
        <button type="button" class="btn btn-outline-primary"
          onclick='callStockModal("1", <?php echo json_encode($locations); ?>);'>Add</button>
        <button type="button" class="btn btn-outline-primary"
          onclick='callStockModal("0", <?php echo json_encode($locations); ?>);'>Move</button>
        <button type="button" class="btn btn-outline-primary"
          onclick='callStockModal("-1", <?php echo json_encode($locations); ?>);'>Reduce</button>
      </div>
      <br><br>

      <h5>Part of:</h5>
      <?php
      include '../config/part-in-boms-columns.php';
      $bom_list = getPartInBoms($conn, $part_id);
      buildPartInBomsTable($db_columns, $nice_columns, $bom_list);?>
      <h5>Datasheet:</h5>
      <h5>Image:</h5>

    </div>
    <div class="tab-pane fade" id="partStockHistory" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
      <?php include 'stock-history.php'; ?>
    </div>
  </div>
</div>

<!-- Click listeners for buttons on stock changing modals -->
<script>
  <?php include '../assets/js/stockChanges.js'; ?>
</script>