import {
    bootstrapLocationsListTable,
    inlineProcessing,
    bootstrapTableSmallify,
    defineLocationsListTableActions,
    rebuildLocationsTable
} from "../tables";

import { attachDeleteRowsHandler } from "../toolbar/toolbar";
import { makeTableWindowResizable } from '../custom';
import { ResourceCreator } from "../resourceCreator";



export function initializeLocationsView() {
    bootstrapLocationsListTable();

    var $table = $('#locations_list_table');
    var $menu = $('#bom_list_table_menu');
    inlineProcessing();
    bootstrapTableSmallify();
    defineLocationsListTableActions($table, $menu)
    makeTableWindowResizable();

    // $('#toolbarAddButton').click(function () {
    //     callLocationEntryModal();
    // });

    const newLocationCreator = new ResourceCreator({
        type: 'location',
        endpoint: '/location.create',
        newIdName: 'Location ID',
        inputForm: '#locationEntryForm',
        inputFields: [
            { name: 'location_name', selector: '#addLocationName' },
            { name: 'location_description', selector: '#addLocationDescription' }
        ],
        inputModal: '#mLocationEntry',
        addButton: '#addLocation',
        tableRebuildFunction: rebuildLocationsTable
    });

    $('#toolbarAddButton').click(function () {
        newLocationCreator.showModal();
        newLocationCreator.attachAddButtonClickListener();
    });

    attachDeleteRowsHandler('locations_list_table', 'locations', 'location_id', rebuildLocationsTable);


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