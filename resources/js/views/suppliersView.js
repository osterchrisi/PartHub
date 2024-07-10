import {
    bootstrapSuppliersListTable,
    defineSuppliersListTableActions,
    rebuildSuppliersTable
} from "../tables";

import { loadSelectedRow } from '../custom';


import { attachDeleteRowsHandler } from "../toolbar/toolbar";

import { ResourceCreator } from "../resourceCreator";

export function initializeSuppliersView() {
    bootstrapSuppliersListTable();

    var $table = $('#suppliers_list_table');
    var $menu = $('#bom_list_table_menu');

    defineSuppliersListTableActions($table, $menu);
    loadSelectedRow('supplier', 'suppliers_list_table');

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
        newSupplierCreator.attachAddButtonClickListener();
    });

    attachDeleteRowsHandler('suppliers_list_table', 'suppliers', 'supplier_id', rebuildSuppliersTable);
};