<?php
// Putting it into the session array for stockChanges.php to use
// $_SESSION['stock_levels'] = $stock_levels;

// Get available locations
// $locations = getLocations($conn, $user_id);

// // Debug
echo '<pre>';
print_r($part);
print_r($total_stock);
echo '</pre>';
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
            @include('parts.stockTable')
            <!-- Location / Quantity Table -->
            <?php
            //   buildTable($db_columns, $nice_columns, $stock_levels);
            ?>

            <!-- Stock movement buttons -->
            <div class="input-group">
                <input type="text" class="form-control form-control-sm" placeholder="Stock:" disabled readonly>
                <button type="button" class="btn btn-sm btn-outline-primary"
                    onclick='callStockModal("1", <?php
                    //   echo json_encode($locations) . ", $part_id";
                    ?>);'>Add</button>
                <button type="button" class="btn btn-sm btn-outline-primary"
                    onclick='callStockModal("0", <?php
                    //   echo json_encode($locations) . ", $part_id";
                    ?>);'>Move</button>
                <button type="button" class="btn btn-sm btn-outline-primary"
                    onclick='callStockModal("-1", <?php
                    //   echo json_encode($locations) . ", $part_id";
                    ?>);'>Reduce</button>
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
    });
</script>
