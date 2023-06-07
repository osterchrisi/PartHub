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

        // Get query string from the URL and create a URLSearchParams object
        const queryString = window.location.search;
        const searchParams = new URLSearchParams(queryString);

        // Manipulate the "search" value and update it in the URL
        let searchValue = searchParams.get('search');
        searchValue = inputVal;
        searchParams.set('search', searchValue);
        var modifiedQueryString = searchParams.toString();

        // Query database and rebuild partstable with result
        modifiedQueryString = '?' + modifiedQueryString;
        rebuildPartsTable(modifiedQueryString);
    });

    initializePopovers();

    attachDeleteRowsHandler($table, 'parts', 'part_id', rebuildPartsTable);


    // Get locations and attach click listener to "Add" button in toolbar
    $.ajax({
        url: '/locations.get',
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

    /**
     * Show location divs after potentially
     * having hidden them in the stock modal when hiding the modal
     * @return void
     */
    $('#mAddStock').on('hidden.bs.modal', function () {
        $('#FromStockLocationDiv-row').show();
        $('#ToStockLocationDiv-row').show();
    })
});