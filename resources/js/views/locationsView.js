import {
    bootstrapLocationsListTable,
    defineLocationsListTableActions,
    rebuildLocationsTable
} from "../tables";

import { TableRowManager } from "../TableRowManager";


import { attachDeleteRowsHandler } from "../toolbar/toolbar";
import { ResourceCreator } from "../resourceCreator";



export function initializeLocationsView() {
    bootstrapLocationsListTable();

    var $table = $('#locations_list_table');
    var $menu = $('#bom_list_table_menu');

    const tableRowManager = new TableRowManager('#locations_list_table', 'location');
    tableRowManager.loadSelectedRow();
    defineLocationsListTableActions($table, $menu, tableRowManager);

    const newLocationCreator = new ResourceCreator({
        type: 'location',
        endpoint: '/location.create',
        table_name: '#locations_list_table',
        newIdName: 'Location ID',
        inputForm: '#locationEntryForm',
        inputFields: [
            { name: 'location_name', selector: '#addLocationName' },
            { name: 'location_description', selector: '#addLocationDescription' }
        ],
        inputModal: '#mLocationEntry',
        addButton: '#addLocation'
    }, [rebuildLocationsTable]);

    $('#toolbarAddButton').click(function () {
        newLocationCreator.showModal();
    });

    attachDeleteRowsHandler('locations_list_table', 'locations', 'location_id', rebuildLocationsTable);
};