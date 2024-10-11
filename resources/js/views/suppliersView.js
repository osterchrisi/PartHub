import {
    bootstrapSuppliersListTable,
    defineSuppliersListTableActions,
    rebuildSuppliersTable
} from "../tables";

import { TableRowManager } from "../TableRowManager";


import { attachDeleteRowsHandler } from "../toolbar/toolbar";

import { ResourceCreator } from "../resourceCreator";

export function initializeSuppliersView() {
    bootstrapSuppliersListTable();

    var $table = $('#suppliers_list_table');
    var $menu = $('#bom_list_table_menu');

    const tableRowManager = new TableRowManager('#suppliers_list_table', 'supplier');
    tableRowManager.loadSelectedRow();
    defineSuppliersListTableActions($table, $menu, tableRowManager);

    const newSupplierCreator = new ResourceCreator({
        type: 'supplier',
        endpoint: '/supplier.create',
        table_name: '#suppliers_list_table',
        newIdName: 'Supplier ID',
        inputForm: '#supplierEntryForm',
        inputFields: [
            { name: 'supplier_name', selector: '#addSupplierName' },
        ],
        inputModal: '#mSupplierEntry',
        addButton: '#addSupplier'
    }, [rebuildSuppliersTable]);

    $('#toolbarAddButton').click(function () {
        newSupplierCreator.showModal();
    });

    attachDeleteRowsHandler('suppliers_list_table', 'suppliers', 'supplier_id', rebuildSuppliersTable);
};