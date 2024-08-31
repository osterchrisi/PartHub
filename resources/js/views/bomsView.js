import {
    bootstrapBomListTable,
    defineBomListTableActions,
    rebuildBomListTable
} from '../tables';

import { StockManager } from '../stockManager';

import { attachDeleteRowsHandler, attachAssembleBomHandler, attachAddBomHandler } from '../toolbar/toolbar';

import { loadSelectedRow } from '../custom';

export function initializeBomsView() {
    bootstrapBomListTable();

    var $table = $('#bom_list_table');
    var $menu = $('#bom_list_table_menu');
    defineBomListTableActions($table, $menu);

    attachDeleteRowsHandler('bom_list_table', 'boms', 'bom_id', rebuildBomListTable);
    attachAssembleBomHandler('bom_list_table');
    attachAddBomHandler();
    loadSelectedRow('bom', 'bom_list_table');

    const stockManager = new StockManager();

    $.ajax({
        url: '/locations.get',
        dataType: 'json',
        success: function (locations) {
            stockManager.fromStockLocationDropdown('bomAssembleLocationDiv', locations);
        },
        error: function (error) {
            console.log(error);
        }
    });


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

