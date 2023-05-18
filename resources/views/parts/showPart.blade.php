<?php
// // Debug
// echo '<pre>';
// print_r($part);
// print_r($total_stock);
// echo '</pre>';
?>

<div class="container-fluid">
    <br>
    <h4>
        {{ $part['part_name'] }}
    </h4>
    <h5>Total stock:
        {{ $total_stock }}
    </h5>

    <!-- Parts Tabs -->
    <ul class="nav nav-tabs" id="partsTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="partStockInfoTabToggler" data-bs-toggle="tab" data-bs-target="#partStockInfoTab"
                type="button" role="tab">Info</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="partStockHistoryTabToggler" data-bs-toggle="tab"
                data-bs-target="#partStockHistoryTab" type="button" role="tab">Stock History</button>
        </li>
    </ul>

    <!-- Tabs Content -->
    <div class="tab-content" id="partsTabsContent">
        <div class="tab-pane fade" id="partStockInfoTab" role="tabpanel" tabindex="0">
            <br>
            <!-- Location / Quantity Table -->
            @include('parts.stockTable')


            <!-- Stock movement buttons -->
            <div class="input-group">
                <input type="text" class="form-control form-control-sm" placeholder="Stock:" disabled readonly>
                <button type="button" class="btn btn-sm btn-outline-primary" id="addStockButton">Add</button>
                <button type="button" class="btn btn-sm btn-outline-primary" id="moveStockButton">Move</button>
                <button type="button" class="btn btn-sm btn-outline-primary" id="reduceStockButton">Reduce</button>
            </div>
            <br><br>

            <h5>Part of:</h5>
            <?php
            //   include '../config/part-in-boms-columns.php';
            //   $bom_list = getPartInBoms($conn, $part_id);
            //   buildPartInBomsTable($db_columns, $nice_columns, $bom_list);
            ?>
            <br>
            <h5>Datasheet:</h5>
            <br>
            <h5>Image:</h5>

        </div>
        <div class="tab-pane fade" id="partStockHistoryTab" role="tabpanel" aria-labelledby="profile-tab"
            tabindex="0">
            <?php
            // include 'stock-history.php';
            ?>
        </div>
    </div>
</div>

<!-- Include custom JS -->
<script>
    <?php
    //   include '../assets/js/stockChanges.js';
    ?>
    <?php
    //   include '../assets/js/tables.js';
    ?>
    $(document).ready(function() {
        loadActiveTab();
        addActiveTabEventListeners();
        bootstrapPartInBomsTable();
        bootstrapTableSmallify();

        // Add Stock Button
        $.ajax({
            url: '/locations.get',
            dataType: 'json',
            success: function(locations) {
                $('#addStockButton').click(function() {
                    callStockModal("1", locations);
                });
            },
            error: function(error) {
                console.log(error);
            }
        })

        // Move Stock Button
        $.ajax({
            url: '/locations.get',
            dataType: 'json',
            success: function(locations) {
                $('#moveStockButton').click(function() {
                    callStockModal("0", locations);
                });
            },
            error: function(error) {
                console.log(error);
            }
        })

        // Reduce Stock Button
        $.ajax({
            url: '/locations.get',
            dataType: 'json',
            success: function(locations) {
                $('#reduceStockButton').click(function() {
                    callStockModal("-1", locations);
                });
            },
            error: function(error) {
                console.log(error);
            }
        })
    });
</script>
