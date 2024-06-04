import {
    bootstrapSuppliersListTable,
    inlineProcessing,
    bootstrapTableSmallify,
    defineSuppliersListTableActions,
    rebuildSuppliersTable
} from "../tables";

import { attachDeleteRowsHandler } from "../toolbar/toolbar";
import { makeTableWindowResizable } from '../custom';

import { ResourceCreator } from "../resourceCreator";

export function initializeSuppliersView() {
    bootstrapSuppliersListTable();

    var $table = $('#suppliers_list_table');
    var $menu = $('#bom_list_table_menu');
    inlineProcessing();
    bootstrapTableSmallify();
    makeTableWindowResizable();
    defineSuppliersListTableActions($table, $menu);

    const newSupplierCreator = new ResourceCreator({
        type: 'supplier',
        endpoint: '/supplier.create',
        newIdName: 'Supplier ID',
        inputForm: '#supplierEntryForm',
        inputFields: [
            { name: 'supplier_name', selector: '#addSupplierName' },
        ],
        inputModal: '#mSupplierEntry',
        addButton: '#addSupplier',
        tableRebuildFunction: rebuildSuppliersTable
    });

    $('#toolbarAddButton').click(function () {
        newSupplierCreator.showModal();
        newSupplierCreator.attachAddButtonClickListener();
    });

    attachDeleteRowsHandler('suppliers_list_table', 'suppliers', 'supplier_id', rebuildSuppliersTable);
};