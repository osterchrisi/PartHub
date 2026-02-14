import {
    attachDeleteRowsHandler,
    attachAssembleBomHandler,
    attachAddBomHandler
} from '../toolbar/toolbar';

import { StockManager } from '../Resources/StockManager';
import { TableRowManager } from '../Tables/TableRowManager';
import { TableManager } from '../Tables/TableManager';

export function initializeBomsView() {
    //* Table Manager
    const bomsTableManager = new TableManager({ type: 'bom' });
    bomsTableManager.bootstrapTable();
    bomsTableManager.defineActions();

    attachDeleteRowsHandler('bom_list_table', 'boms', 'bom_id', () => bomsTableManager.rebuildTable());
    attachAssembleBomHandler('bom_list_table');
    attachAddBomHandler();

    //* Table Row Manager
    const tableRowManager = new TableRowManager('#bom_list_table', 'bom');
    tableRowManager.loadSelectedRow();


    //* Stock Manager
    //TODO: dropdownManager sollte so überarbeitet werden, dass er auch sowas hier handeln kann
    const stockManager = new StockManager();
    $.ajax({
        url: '/locations.get',
        dataType: 'json',
        success: function (locations) {
            stockManager.fromStockLocationDropdown('bomAssembleLocationDiv', locations);
        },
        error: function (error) {
            console.log(error);
        }
    });
}

