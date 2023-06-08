import {
  preventTextSelectionOnShift,
  updatePartsInfo,
  updateStockModal,
  updateBomInfo,
  deleteSelectedRows
} from "./custom";

/**
 * Bootstrap the parts table
 * @return void
 */
export function bootstrapPartsTable() {
  $('#parts_table').bootstrapTable({
  });
};

/**
 * Bootstrap the part stock history table
 * @return void
 */
export function bootstrapHistTable() {
  $('#partStockHistoryTable').bootstrapTable({
  });
};

/**
 * Bootstrap the part in BOMs table (part info window)
 * @return void
 */
export function bootstrapPartInBomsTable() {
  $('#partInBomsTable').bootstrapTable({
  });
};

/**
 * Bootstrap the BOM list table
 * @return void
 */
export function bootstrapBomListTable() {
  $('#bom_list_table').bootstrapTable({
  });
};

/**
 * Bootstrap the BOM details table
 * @return void
 */
export function bootstrapBomDetailsTable() {
  $('#BomDetailsTable').bootstrapTable({
  });
};

/**
 * Bootstrap the Locations table
 * @return void
 */
export function bootstrapLocationsListTable() {
  $('#locations_list_table').bootstrapTable({
  });
};

/**
 * Bootstrap the Categories table
 * @return void
 */
export function bootstrapCategoriesListTable() {
  const $table = $('#categories_list_table');
  $table.bootstrapTable({
    rootParentId: '0',
    onPostBody: function () {
      $table.treegrid({
        treeColumn: 0
      })
    }
  });
};

/**
 * Custom Sorter for my stock URLs
 * Remove the href tag and return only the string values
 * Otherwise cells get sorted by the URL which contains part_id
 * @returns void
 */
function NumberURLSorter(a, b) {
  return $(a).text() - $(b).text();
};

/**
 * Create a select element for the inline category dropdown in parts table and populate it with available categories
 * @param {Array} categories Array of associative arrays containing the categories
 * @param {string} currentValue Current text value of the table cell that is edited
 * @returns 
 */
function createInlineCategorySelect(categories, currentValue) {
  // New select element
  var select = $('<select class="form-select-sm">');
  // Iterate over all available categories
  for (var i = 0; i < categories.length; i++) {
    // Create new option for this categorie
    var option = $('<option>').text(categories[i]['category_name']).attr('value', categories[i]['category_id']);
    if (categories[i]['category_name'] === currentValue) {
      // Add 'selected' attribute to the option with the same text value as the value in the table
      //TODO: Better would be ID value, in case two categories would have same text?
      option.attr('selected', true);
    }
    // Append option to select element
    select.append(option);
  }
  return select;
}

/**
 * Defines the actions to perform when a table row is clicked.
 * Attaches a click event listener to the specified table rows and calls the
 * provided callback function with the extracted ID when a row is selected.
 *
 * @param {jQuery} $table - The jQuery object representing the table element
 * @param {function} onSelect - A callback function to call when a row is selected
 */
export function defineTableRowClickActions($table, onSelect) {
  $table.on('click', 'tbody tr', function () {
    if ($table.find('tr.selected-last').length > 0) {
      $table.find('tr.selected-last').removeClass('selected-last');
    }
    $(this).toggleClass('selected-last');
    var id = $(this).data('id'); // get ID from the selected row
    onSelect(id);
  });

  // Prevent text selection on pressing shift for selecting multiple rows
  preventTextSelectionOnShift($table);
}

/**
* Event listener for clicks outside the menu to hide it
* @param {jQuery} $menu - The context menu to hide
*/
function hideMenuOnClickOutside($menu) {
  $(document).on('click', function (event) {
    if (!$menu.is(event.target) && $menu.has(event.target).length === 0) {
      $menu.hide();
    }
  });
}

/**
 * Attach a context menu to a table row that is triggered by right-clicking on a cell
 *
 * @param {jQuery} $table - The table to attach the context menu to
 * @param {jQuery} $menu - The context menu to show when a cell is right-clicked
 * @param {Object} actions - An object containing action names as keys and action functions as values
 */
function onTableCellContextMenu($table, $menu, actions) {
  // Event listener for the right-click event on table cells
  $table.on('contextmenu', 'td', function (event) {
    if (event.which === 3) { // Right-click
      event.preventDefault(); // Inhibit browser context menu

      // Get selected table rows
      const selectedRows = $table.bootstrapTable('getSelections');
      // Extract IDs
      const ids = selectedRows.map(obj => obj._data.id);
      // Extract Footprints - here for later worries
      const footprints = selectedRows.map(obj => obj.Footprint);

      // Show context menu
      showContextMenu($menu, event)

      // Event listeners for the menu items
      $menu.find('.dropdown-item').off('click').on('click', function () {
        // Get action data attribute
        var action = $(this).data('action');

        // Call the appropriate action function based on the action parameter
        actions[action](selectedRows, ids);

        // Hide menu again
        hideContextMenu($menu)
      });
    }
  });

  // Hide context menu upon clicking outside of it
  hideMenuOnClickOutside($menu);
}

function deleteSelectedRowsFromToolbar(table_id, model, id_column, successCallback) {

  //! I had `$table` jquery object instead of `table_id` but it bugs around weirdly
  //! Most likely due to variable scoping, so I just changed it to be a string
  // Get selected table rows
  var selectedRows = $('#' + table_id).bootstrapTable('getSelections');
  console.log("selectedRows: ", selectedRows);

  // Extract IDs
  var ids = selectedRows.map(obj => obj._data.id);

  if (confirm('Are you sure you want to delete ' + selectedRows.length + ' selected row(s)?\n\nThis will also delete the corresponding entries from BOMs, storage locations and stock history.')) {
    deleteSelectedRows(ids, model, id_column, successCallback);
  }
}

/**
 * Rebuild the parts table after adding or deleting parts
 * @param {string} queryString 
 */
export function rebuildPartsTable(queryString) {
  return $.ajax({
    url: '/parts.partsTable' + queryString,
    success: function (data) {
      $('#parts_table').bootstrapTable('destroy'); // Destroy old parts table
      $('#table-window').html(data); // Update div with new table
      bootstrapPartsTable(); // Bootstrap it
      var $table = $('#parts_table');
      var $menu = $('#parts_table_menu');
      definePartsTableActions($table, $menu); // Define table row actions and context menu
      inlineProcessing();
      bootstrapTableSmallify();
    }
  });
}

/**
 * Makes the button and pagination elements in a Bootstrap Table smaller
 */
export function bootstrapTableSmallify() {
  $('.bootstrap-table .btn').addClass('btn-sm');
  $('.bootstrap-table .pagination').addClass('pagination-sm');
  $('.bootstrap-table .form-control').addClass('form-control-sm');
}


/**
 * Rebuild the BOM list table after adding or deleting BOMs
 * @param {string} queryString - The query string to send with the AJAX request
 * @returns {Promise} - A promise that resolves when the table has been rebuilt
 */
export function rebuildBomListTable(queryString) {
  return $.ajax({
    url: '/boms.bomsTable' + queryString,
    success: function (data) {
      $('#bom_list_table').bootstrapTable('destroy'); // Destroy old BOM list table
      $('#table-window').html(data); // Update div with new table
      bootstrapBomListTable(); // Bootstrap it
      var $table = $('#bom_list_table');
      var $menu = $('#bom_list_table_menu');
      defineBomListTableActions($table, $menu); // Define table row actions and context menu
      inlineProcessing();
      bootstrapTableSmallify();
    }
  });
}

/**
 * Defines row click actions and prepares / attaches a context menu for the parts table
 * @param {jQuery} $table - The table element to work
 * @param {jQuery} $menu - The context menu to attach to that table
 */
export function definePartsTableActions($table, $menu) {
  // Define row click actions
  defineTableRowClickActions($table, function (id) {
    updatePartsInfo(id);
    updateStockModal(id);
  });

  // Define context menu actions
  onTableCellContextMenu($table, $menu, {
    delete: function (selectedRows, ids) {
      if (confirm('Are you sure you want to delete ' + selectedRows.length + ' selected row(s)?\n\nThis will also delete the corresponding entries from BOMs, storage locations and stock history.')) {
        deleteSelectedRows(ids, 'parts', 'part_id', rebuildPartsTable); // Also updates table
      }
    },
    edit: function (selectedRows) {
      editSelectedRows(selectedRows);
    },
    customAction1: function (selectedRows) {
      customAction1(selectedRows);
    }
  });
};

/**
 * Defines row click actions and prepares / attaches a context menu for the BOM list table
 * @param {jQuery} $table - The table element to work
 * @param {jQuery} $menu - The context menu to attach to that table
 */
export function defineBomListTableActions($table, $menu) {
  // Define row click actions
  defineTableRowClickActions($table, function (id) {
    updateBomInfo(id);
  });

  // Define context menu actions
  onTableCellContextMenu($table, $menu, {
    delete: function (selectedRows, ids) {
      if (confirm('Are you sure you want to delete ' + selectedRows.length + ' selected row(s)?')) {
        deleteSelectedRows(ids, 'boms', 'bom_id', rebuildBomListTable); // Also updates table
      }
    },
    assemble: function (selectedRows, ids) {
      assembleBoms(selectedRows, ids);
    }
  });
};

/**
 * Displays a context menu at the specified event location.
 * @param {jQuery} $menu - The context menu element.
 * @param {Event} event - The event that triggered the context menu display.
 *                        The event object contains information about the mouse click,
 *                        including the mouse pointer's X and Y coordinates.
 *                        The X and Y coordinates are used to position the context menu.
 */
function showContextMenu($menu, event) {
  $menu.css({
    left: event.pageX + 'px',
    top: event.pageY + 'px',
    display: 'block'
  });
}

/**
 * Hides the context menu.
 * @param {jQuery} $menu - The jQuery object representing the context menu to hide.
 */
function hideContextMenu($menu) {
  $menu.hide();
}

// Inline table cell manipulation of parts_table
//TODO: Extract functions
//TODO: Remove dropdown upon selecting same option again
export function inlineProcessing() {
  $('.bootstrap-table').on('dblclick', '.editable', function (e) {
    var cell = $(this);

    // Check if the cell is already being edited
    if (cell.hasClass('editing')) {
      return;
    }
    else {
      // Add editing class to the cell
      cell.addClass('editing');
    }

    // Get current value
    var currentValue = cell.text();

    // * It's a category cell
    if (cell.hasClass('category')) {
      // Get list of available categories and populate dropdown
      categories = $.ajax({
        type: 'GET',
        url: '/categories.list',
        dataType: 'JSON',
        success: function (response) {
          categories = response;

          // Create select element
          var select = createInlineCategorySelect(categories, currentValue);

          // Append, selectize category dropdown
          appendInlineCategorySelect(cell, select);

          // Need to focus the selectize control
          var selectizeControl = select[0].selectize;
          selectizeControl.focus();

          // Listen for the blur event on the selectize control
          selectizeControl.on('blur', function () {
            // Remove the select element when the selectize dropdown loses focus
            select.remove();
            cell.text(currentValue);
            cell.removeClass('editing');
          });

          // Listen for the Escape keydown event on the document level because selectized element is eating my events
          $(document).on('keydown', function (event) {
            if (event.key === "Escape" && cell.hasClass('editable') && cell.hasClass('category') && cell.hasClass('editing')) {
              select.remove();
              cell.text(currentValue);
              cell.removeClass('editing');
              // Remove the event handler once it has done its job
              $(document).off('keydown');
            }
          });

          // Select element change event handler
          inlineCategorySelectEventHandler(select, cell);

        }
      });
    }
    else { // * It's a text cell
      // Create input field
      var input = $('<textarea class="form-control">').val(currentValue);
      cell.empty().append(input);
      input.focus();

      // Create label for input field
      var label = $('<small class="text-muted">Enter: Confirm</small>');
      cell.append(label);

      // Confirm upon pressing Enter key
      input.keypress(function (event) {
        if (event.keyCode === 13) {
          input.blur();
        }
      });

      // Close input on "Escape" key press
      input.on('keydown', function (event) {
        if (event.key === "Escape") {
          input.remove();
          cell.text(currentValue);
          cell.removeClass('editing');
          return;
        }
      });

      // Enter new value
      input.blur(function () {
        // Get newly entered value
        var new_value = input.val();

        // Update cell with new value
        cell.text(new_value);

        // Get cell id, column name and database table
        // These are encoded in the table data cells
        var id = cell.closest('td').data('id');
        var column = cell.closest('td').data('column');
        var table_name = cell.closest('td').data('table_name');
        var id_field = cell.closest('td').data('id_field');
        console.log(id, id_field, column, table_name, new_value);

        // Call the updating function
        updateCell(id, column, table_name, new_value, id_field);
        cell.removeClass('editing');
      });
    }
  });
};

/**
 * Updates a cell value in the database using AJAX.
 * @param {number} id - The ID of the row containing the cell to be updated.
 * @param {string} column - The name of the column containing the cell to be updated.
 * @param {string} table_name - The name of the database table containing the cell to be updated.
 * @param {string} new_value - The new value to be assigned to the cell.
 * @param {string} id_field - The name of the primary key field in the database table.
 * @returns {object} - A jQuery AJAX object that can be used to handle the success and error events of the request.
 */
function updateCell(id, column, table_name, new_value, id_field) {

  var token = $('input[name="_token"]').attr('value');
  return $.ajax({
    url: '/updateRow',
    type: 'POST',
    data: {
      id: id,
      column: column,
      table_name: table_name,
      new_value: new_value,
      id_field: id_field
    },
    headers: {
      'X-CSRF-TOKEN': token
    },
    success: function (data) {
      console.log('Data updated successfully');
    },
    error: function (xhr) {
      // Handle the error
      if (xhr.status === 419) {
        // Token mismatch error
        alert('CSRF token mismatch. Please refresh the page and try again.');
      } else {
        // Other errors
        alert('Error updating data');
      }
    }
  });
}

function appendInlineCategorySelect(cell, select) {
  cell.empty().append(select);
  select.selectize();
}

function inlineCategorySelectEventHandler(select, cell) {
  select.on('change', function () {
    var new_value = $(this).val(); // Get new selected value

    // Get cell part_id, column name and database table
    // These are encoded in the table data cells
    var id = cell.closest('td').data('id');
    var column = 'part_category_fk';
    var table_name = cell.closest('td').data('table_name');
    var id_field = cell.closest('td').data('id_field');

    // Call the database table updating function
    $.when(updateCell(id, column, table_name, new_value, id_field)).done(function () {
      // Update HTML cell with new value, need to subtract 1 to account for array starting at 0 but categories at 1
      new_value = categories[new_value - 1]['category_name'];
      cell.text(new_value);
      select.remove();
      cell.removeClass('editing');
    })


  });
}

/**
* Displays a modal for assembling one or more BOMs and sends an AJAX request to the server to assemble the BOMs.
* If there are stock shortages the user is notified after the AJAX request is complete and can chose to continue.
* @param {Array} selectedRows - An array of selected rows from the table.
* @param {Array} ids - An array of BOM IDs.
* @returns {void}
*/
function assembleBoms(selectedRows, ids) {
  $('#mBomAssembly').modal('show'); // Show Modal
  $('#btnAssembleBOMs').click(function () {// Attach clicklistener

    q = $("#bomAssembleQuantity").val(); // Quantity
    fl = $("#fromStockLocation").val(); // From Location
    var token = $('input[name="_token"]').attr('value');

    console.log('Assembling BOMs with the following ids: ', ids);

    $.ajax({
      url: '/bom.assemble',
      type: 'POST',
      data: {
        ids: ids,
        assemble_quantity: q,
        from_location: fl
      },
      headers: {
        'X-CSRF-TOKEN': token
      },
      success: function (response) {

        var r = JSON.parse(response);
        if (r.negative_stock.length === 0) {
          //* Do the normal thing here, all requested stock available
          console.log("All requested stock was available");
          console.log(r);

          $('#mBomAssembly').modal('hide'); // Hide Modal
          updateBomInfo(ids[ids.length - 1]); // Update BOM info window with last BOM ID in array
          //TODO: Also select in table
        }
        else {
          //* User permission required

          console.log("Not all requested stock was available");
          console.log(r);

          // Display warning and missing stock table
          $('#btnAssembleBOMs').attr('disabled', true);
          var message = "<div class='alert alert-warning'>There is not enough stock available for " + r.negative_stock.length + " parts. Do you want to continue anyway?<br>";
          message += "<div style='text-align:right;'><button type='button' class='btn btn-secondary btn-sm' data-bs-dismiss='modal'>Cancel</button> <button type='submit' class='btn btn-primary btn-sm' id='btnAssembleBOMsAnyway'>Do It Anyway</button></div></div>"
          message += r.negative_stock_table;
          $('#mBomAssemblyInfo').html(message);

          // Attach click listener to "Do It Anyway" button
          $('#btnAssembleBOMsAnyway').on('click', function () {
            //TODO: Passing ids for updating table after success but this won't work in the future for selectively updating
            continueAnyway(r, ids, token);
          });
        }
        removeClickListeners('#btnAssembleBOMs'); // Remove previously added click listener
      },
      error: function (xhr) {
        // Handle the error
        if (xhr.status === 419) {
          // Token mismatch error
          alert('CSRF token mismatch. Please refresh the page and try again.');
        } else {
          // Other errors
          alert('Error assembling BOM');
        }
      }
    });
  })
}

//TODO: Extract functions
/**
* Send back the changes array with all statuses set to "gtg" (good to got)
* when the user chooses to continue with assembling BOMs even if there isn't enough stock for some parts.
*
* @param {Object} r - An object containing the changes array received from the server for updating the stock changes.
* @param {Array} ids - An array containing the IDs of the BOMs that need to be updated.
* @returns {void}
*/
function continueAnyway(r, ids, token) {
  //TODO: Recieving ids for updating table after success but this won't work in the future for selectively updating
  // Change all statuses to "good to go"
  for (const change of r.changes) {
    change.status = 'gtg';
  }

  // Call the stock changing script with the already prepared stock changes
  $.ajax({
    url: '/parts.prepareStockChanges',
    type: 'POST',
    data: { stock_changes: r.changes },
    headers: {
      'X-CSRF-TOKEN': token
    },
    success: function (response) {
      console.log(response);
      $('#mBomAssembly').on('hidden.bs.modal', function (e) {
        $('#bomAssemblyForm')[0].reset();
        $('#mBomAssemblyInfo').empty();
        $('#btnAssembleBOMs').attr('disabled', false);
        $(this).modal('dispose');
      }).modal('hide');
      updateBomInfo(ids[ids.length - 1]); // Update BOM info with last BOM ID in array
      // $('#mBomAssembly').modal('dispose'); // Hide Modal
    },
    error: function (xhr) {
      // Handle the error
      if (xhr.status === 419) {
        // Token mismatch error
        alert('CSRF token mismatch. Please refresh the page and try again.');
      } else {
        // Other errors
        alert('Error updating data');
      }
    }
  });
}

/**
 *Attaches a click handler to the Delete button in the toolbar
 *@param {jQuery object} $table - jQuery object representing the table that the rows will be deleted from
 */
export function attachDeleteRowsHandler(table_id, model, id_column, successCallback) {
  $('#toolbarDeleteButton').click(function () {
    deleteSelectedRowsFromToolbar(table_id, model, id_column, successCallback);
  });
}

//* Not using any of the code below this point, it's for appending a part row. Maybe useful later...
//* You need this button for it: 
//* <button class="btn btn-primary" name="AddNew" id="AddNew" type="button">New Entry</button>
// Click listener for the New Entry button
$(document).ready(function () {
  $('#AddNew').click(function () {
    $.ajax({
      type: "POST",
      url: "../includes/create-part.php",
      dataType: "json",
      success: function (response) {
        console.log("Succes");
        var newId = response.id;
        console.log("new parts id: ", newId);
        createNewRow(newId);
      }
    });
  });
});

// Prepend new row to parts table
function createNewRow(part_id) {
  var $table = $('#parts_table');
  var newRowHtml = '<tr data-id="' + part_id + '">' +
    '<td><input type="text" class="form-control" name="name" value="" required></td>' +
    '<td><input type="text" class="form-control" name="email" value=""></td>' +
    '<td><input type="text" class="form-control" name="phone" value=""></td>' +
    '<td><input type="text" class="form-control" name="phone" value=""></td>' +
    '<td><input type="text" class="form-control" name="phone" value=""></td>' +
    '<td><input type="text" class="form-control" name="phone" value=""></td>' +
    '<td><input type="text" class="form-control" name="phone" value=""></td>' +
    '<td><button class="btn btn-sm btn-success save-new-row">OK</button><button class="btn btn-sm btn-danger cancel-new-row">Cncl</button></td>' +
    '</tr>';
  $table.prepend(newRowHtml);
}

// Placeholder function for inserting new row into DB
$('.save-new-row').click(function () {
  var name = $(this).closest('tr').find('input[name="name"]').val();
  var email = $(this).closest('tr').find('input[name="email"]').val();
  var phone = $(this).closest('tr').find('input[name="phone"]').val();
  var data = {
    'name': name,
    'email': email,
    'phone': phone
  };
  $.ajax({
    url: 'insert.php',
    type: 'POST',
    data: data,
    success: function (response) {
      // Refresh the table
      $('#parts_table').bootstrapTable('refresh');
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.log(textStatus, errorThrown);
    }
  });
});