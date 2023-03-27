<?php
include '../config/stock-history-columns.php';
$basename = basename(__FILE__);
$stock_history = getPartStockHistory($conn, $part_id);
// echo "<pre>";
// print_r($stock_history);
// echo "</pre>";
?>

<div class="container-fluid">
    <!-- Stock History Table -->

    <?php
    buildTable($db_columns, $nice_columns, $stock_history);
    ?>