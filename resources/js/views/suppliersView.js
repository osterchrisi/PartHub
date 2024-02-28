import {
    bootstrapSuppliersListTable,
    inlineProcessing,
    bootstrapTableSmallify,
    defineSuppliersListTableActions,
    rebuildSuppliersTable
} from "../tables";

import { callSupplierEntryModal } from '../supplierEntry';
import { attachDeleteRowsHandler } from "../toolbar/toolbar";

export function initializeSuppliersView() {
    bootstrapSuppliersListTable();

    var $table = $('#suppliers_list_table');
    var $menu = $('#bom_list_table_menu');
    inlineProcessing();
    bootstrapTableSmallify();
    defineSuppliersListTableActions($table, $menu);

    $('#toolbarAddButton').click(function () {
        callSupplierEntryModal();
    });

    attachDeleteRowsHandler('suppliers_list_table', 'suppliers', 'supplier_id', rebuildSuppliersTable);
};