import {
    loadActiveTab,
    addActiveTabEventListeners
} from "./custom";

import {
    bootstrapPartInBomsTable,
    bootstrapHistTable,
    bootstrapTableSmallify
} from "./tables";

export function initializeShowPart(part_id) {
    loadActiveTab('parts', '{{ $tabId1 }}');
    addActiveTabEventListeners('parts');
    bootstrapPartInBomsTable();
    bootstrapHistTable();
    bootstrapTableSmallify();

    $.ajax({
        url: '/locations.get',
        dataType: 'json',
        success: function (locations) {
            // Add Stock Button
            $('#addStockButton').click(function () {
                callStockModal("1", locations, part_id);
            });
            // Move Stock Button
            $('#moveStockButton').click(function () {
                callStockModal("0", locations, part_id);
            });
            // Reduce Stock Button
            $('#reduceStockButton').click(function () {
                callStockModal("-1", locations, part_id);
            });
        },
        error: function (error) {
            console.log(error);
        }
    })
};