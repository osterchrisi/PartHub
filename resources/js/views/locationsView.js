import { TableRowManager } from "../../Tables/TableRowManager";
import { attachDeleteRowsHandler } from "../toolbar/toolbar";
import { ResourceCreator } from "../Resources/ResourceCreators/ResourceCreator";
import { TableManager } from "../../Tables/TableManager";



export function initializeLocationsView() {
    const locationsTableManager = new TableManager({
        type: 'location'
    });
    locationsTableManager.bootstrapTable();
    locationsTableManager.defineActions();

    const tableRowManager = new TableRowManager('#locations_list_table', 'location');
    tableRowManager.loadSelectedRow();

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
    }, []);

    $('#toolbarAddButton').click(function () {
        newLocationCreator.showModal();
    });

    attachDeleteRowsHandler('locations_list_table', 'locations', 'location_id', () => locationsTableManager.rebuildTable());
};