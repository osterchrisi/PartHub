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
    // initializeMultiSelect('cat-select');

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

    // Experimental ajax search
    $('#search').on("keyup input", function () {
        /* Get input value on change */
        var inputVal = $(this).val();
        var resultDropdown = $(this).siblings(".result");
        $.get("../includes/buildPartsTable.php", {
            term: inputVal
        }).done(function (data) {
            var querystring = "?search=" + inputVal;
            rebuildPartsTable(querystring);
        });
    });

    // Set search input value on click of result item
    //! Not in use actually
    $(document).on("click", ".result p", function () {
        $(this).parents(".search-box").find('input[type="text"]').val($(this).text());
        $(this).parent(".result").empty();
    });

    initializePopovers();
    attachDeleteRowsHandler();

    //TODO: It's silly to do it this way...
    //! Commenting out to make table appear for now
    // $('#toolbarAddButton').click(function() {
    //     callPartEntryModal(<?php
    // echo json_encode($locations);
    //
    // ?>);
    // });

});