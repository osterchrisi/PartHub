import { TableRowManager } from "../../Tables/TableRowManager";
import { TableManager } from "../../Tables/TableManager";
import { SimpleResourceCreator } from "../Resources/ResourceCreators/SimpleResourceCreator";
import { attachDeleteRowsHandler } from "../toolbar/toolbar";

export function initializeSuppliersView() {
    const suppliersTableManager = new TableManager({
        type: 'supplier'
    });
    suppliersTableManager.bootstrapTable();
    suppliersTableManager.defineActions();

    const tableRowManager = new TableRowManager('#suppliers_list_table', 'supplier');
    tableRowManager.loadSelectedRow();

    const supplierCreator = new SimpleResourceCreator({
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
    });

    $('#toolbarAddButton').click(function () {
        supplierCreator.showModal();
    });

    attachDeleteRowsHandler('suppliers_list_table', 'suppliers', 'supplier_id', () => suppliersTableManager.rebuildTable());
};