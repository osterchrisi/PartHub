// import {
//     bootstrapPartsTable,
//     definePartsTableActions,
//     inlineProcessing,
//     bootstrapTableSmallify,
//     rebuildPartsTable,
//     initializePopovers,
//     attachDeleteRowsHandler
// } from "./tables";

// import {
//     focusStockChangeQuantity,
//     focusNewPartName
// } from "./custom";

$(document).ready(function () {
    //! Commenting this out to make it work laravel
    initializeMultiSelect('cat-select');

    // This was already commented out
    // sendFormOnDropdownChange();

    bootstrapPartsTable();
    var $table = $('#parts_table');
    var $menu = $('#parts_table_menu');
    definePartsTableActions($table, $menu);
    inlineProcessing();
    bootstrapTableSmallify();

    focusStockChangeQuantity();
    focusNewPartName();

    // Need to re-smallify after hiding / showing columns
    $('.bootstrap-table').on('column-switch.bs.table page-change.bs.table', function () {
        bootstrapTableSmallify();
    });

    // Experimental ajax search{
    $('#search').on("keyup", function () {
        // Get input value on change
        var inputVal = $(this).val();

        // Query database and rebuild partstable with result
        var querystring = "?search=" + inputVal;
        rebuildPartsTable(querystring);
    });

    initializePopovers();
    attachDeleteRowsHandler();


    // Get locations and attach click listener to "Add" button in toolbar
    $.ajax({
        url: '/locations',
        dataType: 'json',
        success: function (locations) {
            $('#toolbarAddButton').click(function () {
                callPartEntryModal(locations);
            });
        },
        error: function (error) {
            console.log(error);
        }
    })
});