import {
    bootstrapLocationsListTable,
    inlineProcessing,
    bootstrapTableSmallify,
    defineLocationsListTableActions,
    rebuildLocationsTable
} from "../tables";

import { callLocationEntryModal } from '../locationEntry';
import { attachDeleteRowsHandler } from "../toolbar/toolbar";
import { makeTableWindowResizable } from '../custom';


export function initializeLocationsView() {
    bootstrapLocationsListTable();

    var $table = $('#locations_list_table');
    var $menu = $('#bom_list_table_menu');
    inlineProcessing();
    bootstrapTableSmallify();
    defineLocationsListTableActions($table, $menu)
    makeTableWindowResizable();

    $('#toolbarAddButton').click(function () {
        callLocationEntryModal();
    });

    attachDeleteRowsHandler('locations_list_table', 'locations', 'location_id', rebuildLocationsTable);


    // sendFormOnDropdownChange();

    // Experimental ajax search
    // $('#search').on("keyup input", function() {
    //     /* Get input value on change */
    //     var inputVal = $(this).val();
    //     var resultDropdown = $(this).siblings(".result");
    //     $.get("../includes/buildBomListTable.php", {
    //         term: inputVal
    //     }).done(function(data) {
    //         var querystring = "?search=" + inputVal;
    //         rebuildBomListTable(querystring);
    //     });
    // });
};