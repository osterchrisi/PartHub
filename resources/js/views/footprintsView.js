import { TableRowManager } from "../Tables/TableRowManager";
import { TableManager } from "../Tables/TableManager";
import { SimpleResourceCreator } from "../Resources/ResourceCreators/SimpleResourceCreator";
import { attachDeleteRowsHandler } from "../toolbar/toolbar";

export function initializeFootprintsView() {
    //* Table Manager
    const footprintsTableManager = new TableManager({
        type: 'footprint'
    });
    footprintsTableManager.bootstrapTable();
    footprintsTableManager.defineActions();

    //* Table Row Manager
    const tableRowManager = new TableRowManager('#footprints_list_table', 'footprint');
    tableRowManager.loadSelectedRow();

    //* Resource Creator
    const footprintCreator = new SimpleResourceCreator({
        type: 'footprint',
        endpoint: '/footprint.create',
        table_name: '#footprints_list_table',
        newIdName: 'Footprint ID',
        inputForm: '#footprintEntryForm',
        inputFields: [
            { name: 'footprint_name', selector: '#addFootprintName' },
            { name: 'footprint_alias', selector: '#addFootprintAlias' }
        ],
        inputModal: '#mFootprintEntry',
        addButton: '#addFootprint'
    });

    $('#toolbarAddButton').click(function () {
        footprintCreator.showModal();
    });

    attachDeleteRowsHandler('footprints_list_table', 'footprints', 'footprint_id', () => footprintsTableManager.rebuildTable());
};