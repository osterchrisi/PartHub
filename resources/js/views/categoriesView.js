import {
    bootstrapCategoriesListTable,
    bootstrapTableSmallify,
    defineCategoriesListTableActions
} from "../tables";

import { makeTableWindowResizable } from '../custom.js';

export function initializeCategoriesView() {
    bootstrapCategoriesListTable();

    var $table = $('#categories_list_table');
    var $menu = $('#bom_list_table_menu');
    defineCategoriesListTableActions($table, $menu);
    // inlineProcessing();
    bootstrapTableSmallify();
    makeTableWindowResizable();


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