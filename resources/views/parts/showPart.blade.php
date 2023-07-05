<?php
// Debug
// echo '<pre>';
// print_r($part);
// print_r($total_stock);
// print_r($bom_list);
// print_r($bomTableHeaders);
// print_r($stock_history);
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
    <x-tablist id="partsTabs" tabId1="{{ $tabId1 }}" />
        <x-tab-button id="{{ $tabId1 }}" toggleTarget="{{ $tabToggleId1 }}" tabText="{{ $tabText1 }}" />
        <x-tab-button id="{{ $tabId2 }}" toggleTarget="{{ $tabToggleId2 }}" tabText="{{ $tabText2 }}" />
    </ul>

    <!-- Tabs Content -->
    <div class="tab-content" id="partsTabsContent">
        {{-- Info Tab --}}
        <div class="tab-pane fade" id="{{ $tabToggleId1 }}" role="tabpanel" tabindex="0">
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

            @include('parts.bomTable')
            
            <br>
            <h5>Datasheet:</h5>
            <br>
            <h5>Image:</h5>

        </div>
        {{-- Stock History Tab --}}
        <div class="tab-pane fade" id="{{ $tabToggleId2 }}" role="tabpanel" tabindex="0">

            @include('parts.stockHistoryTable')

        </div>
    </div>
</div>