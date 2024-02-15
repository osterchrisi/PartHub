import {
    bootstrapFootprintsListTable,
    inlineProcessing,
    bootstrapTableSmallify,
    defineFootprintsListTableActions,
    rebuildFootprintsTable
} from "../tables";

import { callFootprintEntryModal } from '../footprintEntry';
import { attachDeleteRowsHandler } from "../toolbar/toolbar";

export function initializeFootprintsView() {
    bootstrapFootprintsListTable();

    var $table = $('#footprints_list_table');
    var $menu = $('#bom_list_table_menu');
    inlineProcessing();
    bootstrapTableSmallify();
    defineFootprintsListTableActions($table, $menu)

    $('#toolbarAddButton').click(function () {
        callFootprintEntryModal();
    });

    attachDeleteRowsHandler('footprints_list_table', 'footprints', 'footprint_id', rebuildFootprintsTable);

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