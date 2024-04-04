import {
    bootstrapBomListTable,
    defineBomListTableActions,
    inlineProcessing,
    bootstrapTableSmallify,
    rebuildBomListTable
} from '../tables';

import {
    fromStockLocationDropdown
} from '../stockChanges';

import { attachDeleteRowsHandler, attachAssembleBomHandler, attachAddBomHandler } from '../toolbar/toolbar';

import { makeTableWindowResizable } from '../custom';

export function initializeBomsView() {
    bootstrapBomListTable();

    var $table = $('#bom_list_table');
    var $menu = $('#bom_list_table_menu');
    defineBomListTableActions($table, $menu);
    inlineProcessing();
    bootstrapTableSmallify();
    makeTableWindowResizable();

    attachDeleteRowsHandler('bom_list_table', 'boms', 'bom_id', rebuildBomListTable);
    attachAssembleBomHandler('bom_list_table');
    attachAddBomHandler();

    $.ajax({
        url: '/locations.get',
        dataType: 'json',
        success: function (locations) {
            fromStockLocationDropdown('bomAssembleLocationDiv', locations);
        },
        error: function (error) {
            console.log(error);
        }
    });


    // fromStockLocationDropdown('bomAssembleLocationDiv'
    // , <?php echo json_encode($locations); ?>
    // );
    // sendFormOnDropdownChange();
    // Experimental ajax search
    $('#search').on("keyup input", function () {
        /* Get input value on change */
        var inputVal = $(this).val();
        var resultDropdown = $(this).siblings(".result");
        $.get("../includes/buildBomListTable.php", {
            term: inputVal
        }).done(function (data) {
            var querystring = "?search=" + inputVal;
            rebuildBomListTable(querystring);
        });
    });
}

