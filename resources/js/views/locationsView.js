import {
    bootstrapLocationsListTable,
    enableInlineProcessing,
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
    enableInlineProcessing();
    bootstrapTableSmallify();
    defineLocationsListTableActions($table, $menu)
    makeTableWindowResizable();

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
        addButton: '#addLocation'
    }, [rebuildLocationsTable]);

    $('#toolbarAddButton').click(function () {
        newLocationCreator.showModal();
        newLocationCreator.attachAddButtonClickListener();
    });

    attachDeleteRowsHandler('locations_list_table', 'locations', 'location_id', rebuildLocationsTable);
};