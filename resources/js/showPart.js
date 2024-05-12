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

    // Handle form submission
    $('#imageUploadForm').submit(function (event) {
        // Prevent the default form submission
        event.preventDefault();

        // Serialize the form data
        var formData = new FormData(this);

        // Submit the form data via AJAX
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                fetchImages(currentPartType, currentPartId);
            },
            error: function (xhr, status, error) {
                // Handle any errors that occur during the upload process
                console.error(error);
            }
        });
    });

};