import {
    loadActiveTab,
    addActiveTabEventListeners,
    fetchImages
} from './custom';

import {
    bootstrapPartInBomsTable,
    bootstrapHistTable,
    bootstrapTableSmallify
} from './tables';

import { callStockModal } from './stockChanges';

export function initializeShowPart(part_id) {
    // loadActiveTab('parts', '{{ $tabId1 }}');
    // addActiveTabEventListeners('parts');
    bootstrapPartInBomsTable();
    bootstrapHistTable();
    bootstrapTableSmallify();

    const defaultTab = document.getElementById('partsTabs').dataset.defaultTab; // data-default-tab attribute

    loadActiveTab('parts', defaultTab);
    addActiveTabEventListeners('parts');

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
            // console.log(error);
        }
    })

    var currentPartType = "part"; // Change this to the appropriate type
    var currentPartId = part_id;
    fetchImages(currentPartType, currentPartId);
};