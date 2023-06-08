import {
    bootstrapCategoriesListTable,
    bootstrapTableSmallify
} from "../tables";

export function initializeCategoriesView() {
    bootstrapCategoriesListTable();

    // var $table = $('#locations_list_table');
    // var $menu = $('#bom_list_table_menu');
    // defineBomListTableActions($table, $menu);
    // inlineProcessing();
    bootstrapTableSmallify();


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