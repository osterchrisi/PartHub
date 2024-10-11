import {
    bootstrapFootprintsListTable,
    defineFootprintsListTableActions,
    rebuildFootprintsTable
} from "../tables";

import { TableRowManager } from "../TableRowManager";


import { attachDeleteRowsHandler } from "../toolbar/toolbar";
import { ResourceCreator } from "../resourceCreator";

export function initializeFootprintsView() {
    bootstrapFootprintsListTable();

    var $table = $('#footprints_list_table');
    var $menu = $('#bom_list_table_menu');
    
    const tableRowManager = new TableRowManager('#footprints_list_table', 'footprint');
    tableRowManager.loadSelectedRow();
    defineFootprintsListTableActions($table, $menu, tableRowManager);

    const newFootprintCreator = new ResourceCreator({
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
    }, [rebuildFootprintsTable]);

    $('#toolbarAddButton').click(function () {
        newFootprintCreator.showModal();
    });

    attachDeleteRowsHandler('footprints_list_table', 'footprints', 'footprint_id', rebuildFootprintsTable);
};