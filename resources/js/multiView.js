import {
    bootstrapPartsTable,
    definePartsTableActions,
    enableInlineProcessing,
    rebuildPartsTable,
    defineTableRowClickActions
} from "./tables";

import {
    focusStockChangeQuantity,
    focusNewPartName,
    initializePopovers,
    initializeMultiSelect
} from "./custom";

import {
    bootstrapBomListTable,
    rebuildBomListTable,
} from './tables';

import { initializeShowBom } from "./showBom";


function rebuildBomListTable2(queryString) {
    return $.ajax({
      url: '/boms.bomsTable' + queryString,
      success: function (data) {
        $('#bom_list_table').bootstrapTable('destroy'); // Destroy old BOM list table
        $('#table-only2').html(data); // Update div with new table
        bootstrapBomListTable(); // Bootstrap it
        var $table = $('#bom_list_table');
        var $menu = $('#bom_list_table_menu');
        defineBomListTableActions2($table, $menu); // Define table row actions and context menu
        enableInlineProcessing();
      }
    });
  }

  function defineBomListTableActions2($table, $menu) {
    // Define row click actions
    defineTableRowClickActions($table, function (id) {
      updateBomInfo2(id);
    });
  
    // Define context menu actions
    // onTableCellContextMenu($table, $menu, {
    //   delete: function (selectedRows, ids) {
    //     if (confirm('Are you sure you want to delete ' + selectedRows.length + ' selected row(s)?')) {
    //       deleteSelectedRows(ids, 'boms', 'bom_id', rebuildBomListTable); // Also updates table
    //     }
    //   },
    //   assemble: function (selectedRows, ids) {
    //     assembleBoms(selectedRows, ids);
    //   }
    // });
  };

  function updateBomInfo2(id) {
    $.ajax({
        url: '/bom/' + id,
        type: 'GET',
        data: { id: id, hideNavbar: true },
        success: function (data) {
            // Replace the content of the info window with the loaded PHP page
            $('#info-window2').html(data);
            initializeShowBom();
        },
        error: function (xhr) {
            if (xhr.status === 401) {
                $('#info-window2').html('Your session expired. Please login again.')
            }
            else {
                // Display an error message if the PHP page failed to load
                $('#info-window2').html('Failed to load additional BOM data.');
            }
        }
    });
};
export function initializeMultiView() {
    const q = '';
    rebuildPartsTable(q);
    rebuildBomListTable2(q);
    var $table = $('#bom_list_table');
    var $menu = $('#bom_list_table_menu');
    defineBomListTableActions2($table, $menu);
}