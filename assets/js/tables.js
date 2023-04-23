// BootstrapTable a table
function bootstrapPartsTable() {
  $('#parts_table').bootstrapTable({
  });
};

function bootstrapHistTable() {
  $('#partStockHistoryTable').bootstrapTable({
  });
};

function bootstrapPartInBomsTable() {
  $('#partInBomsTable').bootstrapTable({
  });
};

function bootstrapBomListTable() {
  $('#bom_list_table').bootstrapTable({
  });
};

function bootstrapBomDetailsTable() {
  $('#BomDetailsTable').bootstrapTable({
  });
};

/**
 * Custom Sorter for my stock URLs
 * Remove the href tag and return only the string values
 * Otherwise cells get sorted by the URL which contains part_id
 */
function NumberURLSorter(a, b) {
  return $(a).text() - $(b).text();
};

// Select element for the category dropdown in parts table 
function createCategorySelect(categories, currentValue) {
  var select = $('<select class="form-select-sm">');
  for (var i = 0; i < categories.length; i++) {
    var option = $('<option>').text(categories[i]['category_name']).attr('value', categories[i]['category_id']);
    if (categories[i]['category_name'] === currentValue) {
      option.attr('selected', true);
    }
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
function defineTableRowClickActions($table, onSelect) {
  $table.on('click', 'tr', function () {
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
      var selectedRows = $table.bootstrapTable('getSelections');
      // Extract IDs
      const ids = selectedRows.map(obj => obj._data.id);
      // Extract Footprints - here for later worries
      const footprints = selectedRows.map(obj => obj.Footprint);

      showContextMenu($menu, event)

      // Event listeners for the menu items
      $menu.find('.dropdown-item').off('click').on('click', function () {
        // Get action data attribute
        var action = $(this).data('action');

        // Call the appropriate action function based on the action parameter
        actions[action](selectedRows, ids);

        hideContextMenu($menu)
      });
    }
  });

  // Hide context menu upon clicking outside of it
  hideMenuOnClickOutside($menu);
}

/**
 * Rebuild the parts table after adding or deleting parts
 * @param {string} queryString 
 */
function rebuildPartsTable(queryString) {
  return $.ajax({
    url: '../includes/buildPartsTable.php' + queryString,
    success: function (data) {
      $('#parts_table').bootstrapTable('destroy'); // Destroy old parts table
      $('#table-window').html(data); // Update div with new table
      bootstrapPartsTable(); // Bootstrap it
      var $table = $('#parts_table');
      var $menu = $('#parts_table_menu');
      definePartsTableActions($table, $menu); // Define table row actions and context menu
      inlineProcessing();
    }
  });
}

/**
 * Rebuild the BOM list table after adding or deleting BOMs
 * @param {string} queryString - The query string to send with the AJAX request
 * @returns {Promise} - A promise that resolves when the table has been rebuilt
 */
function rebuildBomListTable(queryString) {
  return $.ajax({
    url: '../includes/buildBomListTable.php' + queryString,
    success: function (data) {
      $('#bom_list_table').bootstrapTable('destroy'); // Destroy old BOM list table
      $('#table-window').html(data); // Update div with new table
      bootstrapBomListTable(); // Bootstrap it
      var $table = $('#bom_list_table');
      var $menu = $('#bom_list_table_menu');
      defineBomListTableActions($table, $menu); // Define table row actions and context menu
      inlineProcessing();
    }
  });
}

/**
 * Defines row click actions and prepares / attaches a context menu for the parts table
 * 
 * @param {jQuery} $table - The table element to work
 * @param {jQuery} $menu - The context menu to attach to that table
 */
function definePartsTableActions($table, $menu) {
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
 * 
 * @param {jQuery} $table - The table element to work
 * @param {jQuery} $menu - The context menu to attach to that table
 */
function defineBomListTableActions($table, $menu) {
  // Define row click actions
  defineTableRowClickActions($table, function (id) {
    updateBomInfo(id);
  });

  // Define context menu actions
  onTableCellContextMenu($table, $menu, {
    delete: function (selectedRows, ids) {
      if (confirm('Are you sure you want to delete ' + selectedRows.length + ' selected row(s)?')) {
        deleteSelectedRows(ids, 'bom_names', 'bom_id', rebuildBomListTable); // Also updates table
      }
    },
    assemble: function (selectedRows, ids) {
      assembleBoms(selectedRows, ids);
    }
  });
};

/**
 * Displays a context menu at the specified event location.
 *
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
 *
 * @param {jQuery} $menu - The jQuery object representing the context menu to hide.
 */
function hideContextMenu($menu) {
  $menu.hide();
}

// Inline table cell manipulation of parts_table
//TODO: Extract functions
//TODO: Remove dropdown upon clicking out of the box or selecting same option again
function inlineProcessing() {
  $('.bootstrap-table').on('dblclick', '.editable', function (e) {
    var $element = $(this);
    var cell = $element;

    // Check if the cell is already being edited
    if (cell.hasClass('editing')) {
      return;
    }

    // Add editing class to the cell
    cell.addClass('editing');

    // Get current value
    var currentValue = $element.text();

    // * It's a category cell
    if (cell.hasClass('category')) {
      // Get list of available categories and populate dropdown
      categories = $.ajax({
        type: 'GET',
        url: '../includes/getCategories.php',
        dataType: 'JSON',
        success: function (response) {
          categories = response;
          console.log("categories1: ", categories);

          // Create select element
          var select = createCategorySelect(categories, currentValue);

          cell.empty().append(select);
          select.selectize();
          select.focus();

          select.on('change', function () {
            var new_value = $(this).val(); // Get new selected value

            // Get cell part_id, column name and database table
            // These are encoded in the table data cells
            var part_id = cell.closest('td').data('id');
            var column = 'part_category_fk';
            var table_name = cell.closest('td').data('table_name');

            // Call the updating function
            $.ajax({
              type: 'GET',
              url: '../includes/update-cell.php',
              data: {
                part_id: part_id,
                column: column,
                table_name: table_name,
                new_value: new_value
              },
              success: function (data) {
                console.log('Data updated successfully');
              },
              error: function (xhr, status, error) {
                console.error('Error updating data');
              },
              error: function (xhr, status, error) {
                console.error(error);
              }
            });
            // Update cell with new value, need to subtract 1 to account for array starting at 0 but categories at 1
            new_value = categories[new_value - 1]['category_name']
            cell.text(new_value);
            select.remove();
            cell.removeClass('editing');
          });
        }
      });
    }
    else { // * It's a text cell
      // Create input field
      var input = $('<textarea class="form-control">').val(currentValue);
      cell.empty().append(input);
      input.focus();

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
        var table_name = cell.closest('td').data('table_name')
        var id_field = cell.closest('td').data('id_field')
        console.log(id, id_field, column, table_name, new_value);

        // Call the updating function
        $.ajax({
          type: 'GET',
          url: '../includes/update-cell.php',
          data: {
            id: id,
            column: column,
            table_name: table_name,
            new_value: new_value,
            id_field: id_field
          },
          success: function (data) {
            console.log('Data updated successfully');
          },
          error: function (xhr, status, error) {
            console.error('Error updating data');
          }
        });
        cell.removeClass('editing');
      });
    }
  });
};

function assembleBoms(selectedRows, ids) {
  $('#mBomAssembly').modal('show'); // Show Modal
  $('#btnAssembleBOMs').click(function () {// Attach clicklistener

    q = $("#bomAssembleQuantity").val(); // Quantity
    fl = $("#fromStockLocation").val(); // From Location

    $.ajax({
      type: 'POST',
      url: '../includes/bom-assembly.php',
      data: {
        ids: ids,
        assemble_quantity: q,
        from_location: fl
      },
      success: function (response) {
        console.log("Response in assembleBoms JS:")
        console.log(response);

        var r = JSON.parse(response);
        if (r.negative_stock.length === 0) {
          //* Do the normal thing here, all requested stock available
          $('#mBomAssembly').modal('hide'); // Hide Modal
          updateBomInfo(ids[ids.length - 1]); // Update BOM info with last BOM ID in array
          //TODO: Also select in table
        }
        else {
          //* User permission required
          // Display warning and missing stock table
          $('#btnAssembleBOMs').attr('disabled', true);
          var message = "<div class='alert alert-warning'>There is not enough stock available for " + r.negative_stock.length + " parts. Do you want to continue anyway?<br>";
          message += "<div style='text-align:right;'><button type='button' class='btn btn-secondary btn-sm' data-bs-dismiss='modal'>Cancel</button> <button type='submit' class='btn btn-primary btn-sm' id='btnAssembleBOMsAnyway'>Do It Anyway</button></div></div>"
          message += r.negative_stock_table;
          $('#mBomAssemblyInfo').html(message);
        }
        removeClickListeners('#btnAssembleBOMs'); // Remove previously added click listener
      }
    });
  })
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