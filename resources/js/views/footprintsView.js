import {
    bootstrapFootprintsListTable,
    inlineProcessing,
    bootstrapTableSmallify,
    defineFootprintsListTableActions,
    rebuildFootprintsTable
} from "../tables";

import { callFootprintEntryModal } from '../footprintEntry';
import { attachDeleteRowsHandler } from "../toolbar/toolbar";
import { makeTableWindowResizable } from '../custom';
import { ResourceCreator } from "../resourceCreator";

export function initializeFootprintsView() {
    bootstrapFootprintsListTable();

    var $table = $('#footprints_list_table');
    var $menu = $('#bom_list_table_menu');
    inlineProcessing();
    bootstrapTableSmallify();
    makeTableWindowResizable();
    defineFootprintsListTableActions($table, $menu)

    // $('#toolbarAddButton').click(function () {
    //     callFootprintEntryModal();
    // });

    const newFootprint = new ResourceCreator({
        type: 'footprint',
        endpoint: '/footprint.create',
        newIdName: 'Footprint ID',
        inputForm: '#footprintEntryForm',
        inputFields: [
            { name: 'footprint_name', selector: '#addFootprintName' },
            { name: 'footprint_alias', selector: '#addFootprintAlias' }
        ],
        inputModal: '#mFootprintEntry',
        addButton: '#addFootprint',
        tableRebuildFunction: rebuildFootprintsTable
    });

    $('#toolbarAddButton').click(function () {
        newFootprint.showModal();
        newFootprint.attachAddButtonClickListener();
    });

    attachDeleteRowsHandler('footprints_list_table', 'footprints', 'footprint_id', rebuildFootprintsTable);
};