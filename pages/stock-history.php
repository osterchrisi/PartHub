<?php
// The stock history tab in the parts-info window
include '../config/stock-history-columns.php';
$basename = basename(__FILE__);
$stock_history = getPartStockHistory($conn, $part_id);
?>

<div class="container-fluid">
    <!-- Stock History Table -->
    <?php buildPartStockHistoryTable($db_columns, $nice_columns, $stock_history); ?>
</div>

<script>
    bootstrapHistTable();
</script>