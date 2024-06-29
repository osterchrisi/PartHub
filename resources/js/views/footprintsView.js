import {
    bootstrapFootprintsListTable,
    defineFootprintsListTableActions,
    rebuildFootprintsTable
} from "../tables";

import { attachDeleteRowsHandler } from "../toolbar/toolbar";
import { ResourceCreator } from "../resourceCreator";

export function initializeFootprintsView() {
    bootstrapFootprintsListTable();

    var $table = $('#footprints_list_table');
    var $menu = $('#bom_list_table_menu');

    defineFootprintsListTableActions($table, $menu)

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
        newFootprintCreator.attachAddButtonClickListener();
    });

    attachDeleteRowsHandler('footprints_list_table', 'footprints', 'footprint_id', rebuildFootprintsTable);
};