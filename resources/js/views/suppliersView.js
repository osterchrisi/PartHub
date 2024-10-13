import { TableRowManager } from "../TableRowManager";
import { TableManager } from "../TableManager";
import { ResourceCreator } from "../ResourceCreator";
import { attachDeleteRowsHandler } from "../toolbar/toolbar";

export function initializeSuppliersView() {
    const suppliersTableManager = new TableManager({
        type: 'supplier'
    });
    suppliersTableManager.bootstrapTable();
    suppliersTableManager.defineActions();

    const tableRowManager = new TableRowManager('#suppliers_list_table', 'supplier');
    tableRowManager.loadSelectedRow();

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
    }, []);

    $('#toolbarAddButton').click(function () {
        newSupplierCreator.showModal();
    });

    attachDeleteRowsHandler('suppliers_list_table', 'suppliers', 'supplier_id', () => suppliersTableManager.rebuildTable());
};