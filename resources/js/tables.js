import {
  preventTextSelectionOnShift,
  updateStockModal,
  deleteSelectedRows,
  removeClickListeners,
  updateInfoWindow
} from "./custom";

import { makeTableWindowResizable } from './custom.js';
import { callCategoryEntryModal } from './categoryEntry.js';
import { ResourceCreator } from "./resourceCreator.js";

// import { fetchDataThenAttachClickListenerAndDefineCategoriesTableActions } from './views/partsView';
import { isAxiosError } from "axios";

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

  // Find the element with the class "fixed-table-toolbar"
  var $fixedTableToolbar = $('#bomInfo .fixed-table-toolbar');

  //* Tryout for a way to display storage places in the BOM details table
  $fixedTableToolbar.append('<div class="row"><div class="col"><div class="columns columns-right btn-group float-right"><div class="keep-open btn-group" title="Columns"><button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-label="Columns" title="Columns" aria-expanded="false"><i class="bi bi-buildings"></i><span class="caret"></span></button><div class="dropdown-menu dropdown-menu-right" style=""><label class="dropdown-item dropdown-item-marker"><input type="checkbox" data-field="Part Name" value="0" checked="checked"> <span>Storage 1</span></label><label class="dropdown-item dropdown-item-marker"><input type="checkbox" data-field="Quantity needed" value="1" checked="checked"> <span>Storage 2</span></label><label class="dropdown-item dropdown-item-marker"><input type="checkbox" data-field="Total stock available" value="2" checked="checked"> <span>Storage 3</span></label><label class="dropdown-item dropdown-item-marker"><input type="checkbox" data-field="Can build" value="3" checked="checked"> <span>Storage 4</span></label></div></div></div></div></div>');
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
 * Bootstrap the Footprints table
 * @return void
 */
export function bootstrapFootprintsListTable() {
  $('#footprints_list_table').bootstrapTable({
  });
};

/**
 * Bootstrap the Suppliers table
 * @return void
 */
export function bootstrapSuppliersListTable() {
  $('#suppliers_list_table').bootstrapTable({
  });
};

//TODO: Extract functions -> Also at editTextCell()->else if->part_categories->treegrid
/**
 * Bootstrap the Categories table
 * @return void
 */
export function bootstrapCategoriesListTable(treeColumn = 1) {
  const $table = $('#categories_list_table');
  $table.bootstrapTable({
    rootParentId: '0',
    onPostBody: function () {
      // Treegrid
      $table.treegrid({
        treeColumn: treeColumn,
      });

      // Edit toggle button click listener
      attachEditCategoriesButtonClickListener();

      //TODO: Use this structure to trigger Deletion / Adding of Categories
      // Attach click listeners to edit buttons
      $('#categories_list_table').on('click', 'tbody .edit-button', function () {
        // Get the parent <tr> element
        var $row = $(this).closest('tr');
        // Extract the data attributes from the <tr> element
        var parentId = $row.data('parent-id');
        var categoryId = $row.data('id');
        // Extract the action from the clicked icon's data attribute
        var action = $(this).data('action');

      });


      //* Delete Category
      //TODO: This info should be encoded into the HTML table like with my other tables
      $('#categories_list_table').on('click', 'tbody .trash-button', function () {
        var $row = $(this).closest('tr');
        var categoryId = [$row.data('id')];

        // Find child categories recursively
        findChildCategoriesFromCategoryTable(categoryId[0], categoryId);

        //! Next up:
        //TODO: Need custom deletion AJAX call / server implementation as I need to take care of potentially nested categories
        deleteSelectedRows(categoryId, 'part_categories', 'category_id', rebuildCategoriesTable);
      });

      //* Add Category
      $('#categories_list_table').on('click', 'tbody .addcat-button', function () {
        var $row = $(this).closest('tr');
        var categoryId = [$row.data('id')];

        const newCategoryCreator = new ResourceCreator({
          type: 'category',
          endpoint: '/category.create',
          newIdName: 'Category ID',
          inputForm: '#categoryEntryForm',
          inputFields: [
            { name: 'category_name', selector: '#addCategoryName' }
          ],
          inputModal: '#mCategoryEntry',
          addButton: '#addCategory', //! Is not in use anymore
          tableRebuildFunction: rebuildPartsTable //! Would be great to rebuild the category table as well!
        },
          { categoryId: categoryId[0] });

        newCategoryCreator.showModal();
        newCategoryCreator.attachAddButtonClickListener(); //! Not necessary anymore
      });
      // callCategoryEntryModal(categoryId);
    }
  });
};

/**
 * Attach the click listener to the "Edit Categories" button. The button toggles the visibility of the Categories Edit column
 */
function attachEditCategoriesButtonClickListener() {
  $('#cat-edit-btn').on('click', function () {
    var columnIndex = 0;
    $('#categories_list_table th[data-field="category_edit"], #categories_list_table td[data-field="category_edit"]').toggle();
  });
}

/**
 * Attach the click listener to the "Toggle Categories" button. The button toggles the visibility of the Categories div in the parts view
 */
export function attachShowCategoriesButtonClickListener() {
  $('#cat-show-btn').on('click', function () {
    $('#category-window').toggle();
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
    // Create new option for this category
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
 * Create a select element for the inline footpring dropdown in parts table and populate it with available footprints
 * This is copy of createInlineCategorySelect!!
 * @param {Array} footprints Array of associative arrays containing the footprints
 * @param {string} currentValue Current text value of the table cell that is edited
 * @returns 
 */
function createInlineFootprintSelect(footprints, currentValue) {
  // New select element
  var select = $('<select class="form-select-sm">');
  // Iterate over all available footprints
  for (var i = 0; i < footprints.length; i++) {
    // Create new option for this footprint
    var option = $('<option>').text(footprints[i]['footprint_name']).attr('value', footprints[i]['footprint_id']);
    if (footprints[i]['footprint_name'] === currentValue) {
      // Add 'selected' attribute to the option with the same text value as the value in the table
      //TODO: Better would be ID value, in case two footprints would have same text?
      option.attr('selected', true);
    }
    // Append option to select element
    select.append(option);
  }
  return select;
}

/**
 * Create a select element for the inline supplier dropdown in parts table and populate it with available suppliers
 * This is copy of createInlineFootprintSelect!!
 * @param {Array} supplier Array of associative arrays containing the suppliers
 * @param {string} currentValue Current text value of the table cell that is edited
 * @returns 
 */
function createInlineSupplierSelect(suppliers, currentValue) {
  // New select element
  var select = $('<select class="form-select-sm">');
  // Iterate over all available suppliers
  for (var i = 0; i < suppliers.length; i++) {
    // Create new option for this supplier
    var option = $('<option>').text(suppliers[i]['supplier_name']).attr('value', suppliers[i]['supplier_id']);
    if (suppliers[i]['supplier_name'] === currentValue) {
      // Add 'selected' attribute to the option with the same text value as the value in the table
      //TODO: Better would be ID value, in case two suppliers would have same text?
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
    if (event.which === 3) {  // Right-click
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

/**
 * Rebuild the parts table after adding or deleting parts
 * @param {string} queryString 
 */
export function rebuildPartsTable(queryString) {
  return $.ajax({
    url: '/parts.partsTable' + queryString,
    success: function (data) {
      $('#parts_table').bootstrapTable('destroy');    // Destroy old parts table
      $('#table-window').html(data);                  // Update div with new table
      bootstrapPartsTable();                          // Bootstrap it
      var $table = $('#parts_table');
      var $menu = $('#parts_table_menu');
      definePartsTableActions($table, $menu);         // Define table row actions and context menu
      inlineProcessing();
      bootstrapTableSmallify();
      makeTableWindowResizable();
    }
  });
}

/**
 * Rebuild the locations table after adding or deleting locations
 * @param {string} queryString 
 */
export function rebuildLocationsTable(queryString) {
  return $.ajax({
    url: '/locations.locationsTable' + queryString,
    success: function (data) {
      $('#locations_list_table').bootstrapTable('destroy');  // Destroy old parts table
      $('#table-window').html(data);                    // Update div with new table
      bootstrapLocationsListTable();                    // Bootstrap it
      var $table = $('#locations_list_table');
      var $menu = $('#parts_table_menu');
      defineLocationsListTableActions($table, $menu);           // Define table row actions and context menu
      inlineProcessing();
      bootstrapTableSmallify();
      makeTableWindowResizable();
    }
  });
}

/**
 * Rebuild the categories table after adding or deleting categories
 */
export function rebuildCategoriesTable() {
  console.log("Rebuilding cat table");
  return $.ajax({
    url: '/categories.categoriesTable',
    success: function (data) {
      $('#categories_list_table').bootstrapTable('destroy');   // Destroy old categories table
      $('#category-window').html(data);                        // Update div with new table
      bootstrapCategoriesListTable();                          // Bootstrap it

      var $table = $('#categories_list_table');
      var $menu = $('#parts_table_menu');

      // //TODO: Seems hacky but works. Otherwise the edit buttons always jump line:
      // $('#category-window').width($('#category-window').width()+1);
      inlineProcessing();
      bootstrapTableSmallify();
      makeTableWindowResizable();

      $.ajax({
        url: '/categories.get',
        dataType: 'json',
        error: function (error) {
          console.log(error);
        }
      }).done(categories => {
        defineCategoriesListInPartsViewTableActions($('#categories_list_table'), $('#bom_list_table_menu'), categories)
      });
    }
  });
}

/**
 * Rebuild the footprints table after adding or deleting footprints
 * @param {string} queryString 
 */
export function rebuildFootprintsTable(queryString) {
  return $.ajax({
    url: '/footprints.footprintsTable' + queryString,
    success: function (data) {
      $('#footprints_list_table').bootstrapTable('destroy'); // Destroy old footprints table
      $('#table-window').html(data);                         // Update div with new table
      bootstrapFootprintsListTable();                        // Bootstrap it
      var $table = $('#footprints_list_table');
      var $menu = $('#parts_table_menu');
      defineFootprintsListTableActions($table, $menu);       // Define table row actions and context menu
      inlineProcessing();
      bootstrapTableSmallify();
      makeTableWindowResizable();
    }
  });
}

/**
 * Rebuild the suppliers table after adding or deleting suppliers
 * @param {string} queryString 
 */
export function rebuildSuppliersTable(queryString) {
  return $.ajax({
    url: '/suppliers.suppliersTable' + queryString,
    success: function (data) {
      $('#suppliers_list_table').bootstrapTable('destroy');  // Destroy old parts table
      $('#table-window').html(data);                    // Update div with new table
      bootstrapSuppliersListTable();                    // Bootstrap it
      var $table = $('#suppliers_list_table');
      var $menu = $('#parts_table_menu');
      defineSuppliersListTableActions($table, $menu);           // Define table row actions and context menu
      inlineProcessing();
      bootstrapTableSmallify();
      makeTableWindowResizable();
    }
  });
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
      $('#table-window').html(data);                  // Update div with new table
      bootstrapBomListTable();                        // Bootstrap it
      var $table = $('#bom_list_table');
      var $menu = $('#bom_list_table_menu');
      defineBomListTableActions($table, $menu);       // Define table row actions and context menu
      inlineProcessing();
      bootstrapTableSmallify();
      makeTableWindowResizable();
    }
  });
}

/**
 * Defines row click actions and prepares / attaches a context menu for the parts table
 * @param {jQuery} $table - The table element to work
 * @param {jQuery} $menu - The context menu to attach to that table
 */
export function definePartsTableActions($table, $menu) {
  // Define what happens when a row gets clicked
  defineTableRowClickActions($table, function (id) {
    updateInfoWindow('part', id)
    updateStockModal(id);
  });

  // Define context menu actions.
  //* Important: selectedRows and ids are extraced in function onTableCellContextMenu itself, not here
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
  // Define what happens when a row gets clicked
  defineTableRowClickActions($table, function (id) {
    updateInfoWindow('bom', id);
  });

  // Define context menu actions
  //* Important: selectedRows and ids are extraced in function onTableCellContextMenu itself, not here
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

export function defineLocationsListTableActions($table, $menu) {
  defineTableRowClickActions($table, function (id) {
    updateInfoWindow('location', id);
  });
}

export function defineFootprintsListTableActions($table, $menu) {
  defineTableRowClickActions($table, function (id) {
    updateInfoWindow('footprint', id);
  });
}

export function defineCategoriesListTableActions($table, $menu) {
  defineTableRowClickActions($table, function (id) {
    updateInfoWindow('category', id);
  });
}

/**
 * Define actions when clicking rows in the Categories List Table.
 * 
 * Extract clicked category, find its children and filter parts table accordingly.
 * Note that filtering is based on bootstrap-tables' filter algo and works on strings, not category IDs.
 * @param {*} $table 
 * @param {*} $menu 
 * @param {*} categories JSON array of available categories
 */
export function defineCategoriesListInPartsViewTableActions($table, $menu, categories) {
  defineTableRowClickActions($table, function (id) {
    const orig_id = id;

    // Array of category and potential child category names as strings for filtering parts table
    var cats = getChildCategoriesNames(categories, orig_id);

    // Filter by categories
    $('#parts_table').bootstrapTable('filterBy', {
      Category: cats
    })
  });
}

/**
 * Fabricate array of category names matching the given category ID and its children.
 * This array is suited to work with bootstrap-tables' filter algorithm
 * @param {*} categories 
 * @param {*} categoryId 
 * @returns 
 */
function getChildCategoriesNames(categories, categoryId) {
  // Initialize an array to store matching category names
  let childCategoriesNames = [];

  // Find the category name corresponding to the provided category ID
  const category = categories.find(cat => cat.category_id === categoryId);
  if (category) {
    childCategoriesNames.push(category.category_name);
  }

  // Helper function to recursively find child categories
  function findChildCategoriesNames(parentCategoryId) {
    // Find categories whose parent category matches the given category ID
    const children = categories.filter(category => category.parent_category === parentCategoryId);

    // Add the names of found children to the result array
    children.forEach(child => {
      childCategoriesNames.push(child.category_name);
      // Recursively find children of children
      findChildCategoriesNames(child.category_id);
    });
  }

  // Find child categories starting from the given category ID
  findChildCategoriesNames(categoryId);

  return childCategoriesNames;
}

/**
 * Recursively find child categories in case a user wants to delete a category
 * @param {string} parentId Category ID of the clicked category and first entry in the categoryId array
 * @param {Array} categoryId Array to store clicked categery and recursive child categories
 */
function findChildCategoriesFromCategoryTable(parentId, categoryId) {
  $('#categories_list_table tbody tr').each(function () {
    var $currentRow = $(this);
    var currentParentId = $currentRow.data('parent-id');
    if (currentParentId === parentId) {
      var childCategoryId = $currentRow.data('id');
      categoryId.push(childCategoryId);
      // Recursively find child categories of this child category
      findChildCategoriesFromCategoryTable(childCategoryId, categoryId);
    }
  });
}

export function defineSuppliersListTableActions($table, $menu) {
  defineTableRowClickActions($table, function (id) {
    updateInfoWindow('supplier', id);
  });
}

/**
 * Displays a context menu at the specified event location inside a table.
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

/**
 * Inline table cell editing of a text cell
 * @param {jQuery} cell The cell being edited
 * @param {string} originalValue The original value of the cell before editing
 */
function editTextCell(cell, originalValue) {
  // Create input field
  var input = $('<textarea class="form-control">').val(originalValue);
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
      cell.text(originalValue);
      cell.removeClass('editing');

      //TODO: Don't really like this solution
      // If exiting through escape happened on categories last in parts view
      if ($('#categories_list_table')) {
        $('#categories_list_table').treegrid({
          treeColumn: 1
        })
      };

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
    // console.log(id, id_field, column, table_name, new_value);

    // Call the updating function
    updateCell(id, column, table_name, new_value, id_field);
    cell.removeClass('editing');

    //TODO: Not great - but works?!
    if (table_name == 'parts') {
      updateInfoWindow('part', id);
    }
    else if (table_name == 'locations') {
      updateInfoWindow('location', id);
    }
    else if (table_name == 'footprints') {
      updateInfoWindow('footprint', id);
    }
    else if (table_name == 'suppliers') {
      updateInfoWindow('supplier', id);
    }
    else if (table_name == 'boms') {
      updateInfoWindow('bom', id);
    }
    else if (table_name == 'part_categories') {
      $('#categories_list_table').treegrid({
        treeColumn: 1
      });
    }

  });
}

/**
 * Inline table cell editing of a category cell in the parts table
 * @param {jQuery} cell The cell being edited
 * @param {string} originalValue The original value of the cell before editing
 */
function editCategoryCell(cell, originalValue) {
  // Changed flag
  var valueChanged = false;
  // Get list of available categories and populate dropdown
  var categories = $.ajax({
    type: 'GET',
    url: '/categories.get',
    dataType: 'JSON',
    success: function (response) {
      categories = response;

      // Create select element
      var select = createInlineCategorySelect(categories, originalValue);

      // Append, selectize category dropdown
      appendInlineCategorySelect(cell, select);

      // Need to focus the selectize control
      var selectizeControl = select[0].selectize;
      selectizeControl.focus();

      // Select element change event handler and callback function to set flag
      inlineCategorySelectEventHandler(select, cell, categories, function changeFlagCallback() {
        valueChanged = true;
      });

      // Listen for the blur event on the selectize control
      selectizeControl.on('blur', function () {
        // Remove the select element when the selectize dropdown loses focus
        select.remove();
        // Change cell text back if value was not changed
        if (!valueChanged) {
          cell.text(originalValue);
        }
        cell.removeClass('editing');
      });

      // Listen for the Escape keydown event on the document level because selectized element is eating my events
      $(document).on('keydown', function (event) {
        if (event.key === "Escape" && cell.hasClass('editable') && cell.hasClass('category') && cell.hasClass('editing')) {
          select.remove();
          // Change cell text back if value was not changed
          if (!valueChanged) {
            cell.text(originalValue);
          }
          cell.removeClass('editing');
          // Remove the event handler once it has done its job
          $(document).off('keydown');
        }
      });
    }
  });
}

/**
 * Inline table cell editing of a footprint cell in the parts table
 * @param {jQuery} cell The cell being edited
 * @param {string} originalValue The original value of the cell before editing
 */
function editFootprintCell(cell, originalValue) {
  // Changed flag
  var valueChanged = false;
  // Get list of available footprints and populate dropdown
  var footprints = $.ajax({
    type: 'GET',
    url: '/footprints.get',
    dataType: 'JSON',
    success: function (response) {
      footprints = response;

      // Create select element
      var select = createInlineFootprintSelect(footprints, originalValue);

      // Append, selectize footprint dropdown
      appendInlineFootprintSelect(cell, select);

      // Need to focus the selectize control
      var selectizeControl = select[0].selectize;
      selectizeControl.focus();

      // Select element change event handler and callback function to set flag
      inlineFootprintSelectEventHandler(select, cell, footprints, function changeFlagCallback() {
        valueChanged = true;
      });

      // Listen for the blur event on the selectize control
      selectizeControl.on('blur', function () {
        // Remove the select element when the selectize dropdown loses focus
        select.remove();
        // Change cell text back if value was not changed
        if (!valueChanged) {
          cell.text(originalValue);
        }
        cell.removeClass('editing');
      });

      // Listen for the Escape keydown event on the document level because selectized element is eating my events
      $(document).on('keydown', function (event) {
        if (event.key === "Escape" && cell.hasClass('editable') && cell.hasClass('footprint') && cell.hasClass('editing')) {
          select.remove();
          // Change cell text back if value was not changed
          if (!valueChanged) {
            cell.text(originalValue);
          }
          cell.removeClass('editing');
          // Remove the event handler once it has done its job
          $(document).off('keydown');
        }
      });
    }
  });
}

/**
 * Inline table cell editing of a supplier cell in the parts table
 * @param {jQuery} cell The cell being edited
 * @param {string} originalValue The original value of the cell before editing
 */
function editSupplierCell(cell, originalValue) {
  // Changed flag
  var valueChanged = false;
  // Get list of available suppliers and populate dropdown
  var suppliers = $.ajax({
    type: 'GET',
    url: '/suppliers.get',
    dataType: 'JSON',
    success: function (response) {
      suppliers = response;

      // Create select element
      var select = createInlineSupplierSelect(suppliers, originalValue);

      // Append, selectize supplier dropdown
      appendInlineSupplierSelect(cell, select);

      // Need to focus the selectize control
      var selectizeControl = select[0].selectize;
      selectizeControl.focus();

      // Select element change event handler and callback function to set flag
      inlineSupplierSelectEventHandler(select, cell, suppliers, function changeFlagCallback() {
        valueChanged = true;
      });

      // Listen for the blur event on the selectize control
      selectizeControl.on('blur', function () {
        // Remove the select element when the selectize dropdown loses focus
        select.remove();
        // Change cell text back if value was not changed
        if (!valueChanged) {
          cell.text(originalValue);
        }
        cell.removeClass('editing');
      });

      // Listen for the Escape keydown event on the document level because selectized element is eating my events
      $(document).on('keydown', function (event) {
        if (event.key === "Escape" && cell.hasClass('editable') && cell.hasClass('supplier') && cell.hasClass('editing')) {
          select.remove();
          // Change cell text back if value was not changed
          if (!valueChanged) {
            cell.text(originalValue);
          }
          cell.removeClass('editing');
          // Remove the event handler once it has done its job
          $(document).off('keydown');
        }
      });
    }
  });
}

/**
 * Inline table cell manipulation of bootstrapped tables
 */
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
    var originalValue = cell.text();

    // * Dropdown cells
    if (cell.hasClass('category')) {
      editCategoryCell(cell, originalValue);
    }
    else if (cell.hasClass('footprint')) {
      editFootprintCell(cell, originalValue);
    }
    else if (cell.hasClass('supplier')) {
      editSupplierCell(cell, originalValue);
    }
    //* It's a text cell
    else {
      editTextCell(cell, originalValue);
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
      // console.log('Data updated successfully');
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

function appendInlineFootprintSelect(cell, select) {
  cell.empty().append(select);
  select.selectize();
}

function appendInlineSupplierSelect(cell, select) {
  cell.empty().append(select);
  select.selectize();
}

function inlineCategorySelectEventHandler(select, cell, categories, changeFlagCallback) {
  select.on('change', function () {
    var selectedValue = $(this).val(); // Get new selected value

    // Get cell part_id, column name and database table
    // These are encoded in the table data cells
    var id = cell.closest('td').data('id');
    var column = 'part_category_fk';
    var table_name = cell.closest('td').data('table_name');
    var id_field = cell.closest('td').data('id_field');

    // Call the database table updating function
    $.when(updateCell(id, column, table_name, selectedValue, id_field)).done(function () {

      // Find category name for a given category ID
      var newValue = categories.find(function (item) {
        return item.category_id === parseInt(selectedValue); // Return true if the item's categry_id matches selectedValue
      });

      // Check if newValue is found and update HTML cell
      if (newValue) {
        newValue = newValue.category_name; // Get the category_name from the found item
        cell.text(newValue);
      } else {
        // console.log("No matching category found for category_id:", selectedValue);
        // Handle the case when no matching category is found
      }

      // Editing aftermath
      select.remove();
      cell.removeClass('editing');
      changeFlagCallback(); // Callback function to set change flag
      $(document).off('keydown'); // Removing the escape handler because it's on document level
    })


  });
}

function inlineFootprintSelectEventHandler(select, cell, footprints, changeFlagCallback) {
  select.on('change', function () {
    var selectedValue = $(this).val(); // Get new selected value

    // Get cell part_id, column name and database table
    // These are encoded in the table data cells
    var id = cell.closest('td').data('id');
    var column = 'part_footprint_fk';
    var table_name = cell.closest('td').data('table_name');
    var id_field = cell.closest('td').data('id_field');

    // Call the database table updating function
    $.when(updateCell(id, column, table_name, selectedValue, id_field)).done(function () {
      // Find footprint name for a given footprint ID
      var newValue = footprints.find(function (item) {
        return item.footprint_id === parseInt(selectedValue); // Return true if the item's footprint_id matches selectedValue
      });

      // Check if newValue is found and update HTML table
      if (newValue) {
        newValue = newValue.footprint_name; // Get the footprint_name from the found item
        cell.text(newValue);
      } else {
        console.log("No matching footprint found for footprint_id:", selectedValue);
      }

      // Editing aftermath
      select.remove();
      cell.removeClass('editing');
      changeFlagCallback(); // Callback function to set change flag
      $(document).off('keydown'); // Removing the escape handler because it's on document level
    });
  });
}

function inlineSupplierSelectEventHandler(select, cell, suppliers, changeFlagCallback) {
  select.on('change', function () {
    var selectedValue = $(this).val(); // Get new selected value

    // Get cell part_id, column name and database table
    // These are encoded in the table data cells
    var id = cell.closest('td').data('id');
    var column = 'part_supplier_fk';
    var table_name = cell.closest('td').data('table_name');
    var id_field = cell.closest('td').data('id_field');

    // Call the database table updating function
    $.when(updateCell(id, column, table_name, selectedValue, id_field)).done(function () {
      // Find supplier name for a given supplier ID
      var newValue = suppliers.find(function (item) {
        return item.supplier_id === parseInt(selectedValue); // Return true if the item's supplier_id matches selectedValue
      });

      // Check if newValue is found and update HTML table
      if (newValue) {
        newValue = newValue.supplier_name; // Get the supplier_name from the found item
        cell.text(newValue);
      } else {
        console.log("No matching supplier found for supplier_id:", selectedValue);
      }

      // Editing aftermath
      select.remove();
      cell.removeClass('editing');
      changeFlagCallback(); // Callback function to set change flag
      $(document).off('keydown'); // Removing the escape handler because it's on document level
    });
  });
}

/**
* Displays a modal for assembling one or more BOMs and sends an AJAX request to the server to assemble the BOMs.
* If there are stock shortages the user is notified after the AJAX request is complete and can chose to continue.
* @param {Array} selectedRows - An array of selected rows from the table.
* @param {Array} ids - An array of BOM IDs.
* @returns {void}
*/
export function assembleBoms(selectedRows, ids) {

  if (ids.length === 0) {
    alert("Please select BOM(s) to be assembled.\nYou can use Ctrl and Shift to select multiple rows");
    return
  }
  $('#mBomAssembly').modal('show');         // Show Modal

  // Attach click listener to the main "Cancel" button of the modal
  $('#btnCancelAssembly').on('click', function () {
    hideBOMAssemblyModalAndCleanup();
  });

  $('#btnAssembleBOMs').click(function () { // Attach clicklistener

    var q = $("#bomAssembleQuantity").val();                // Quantity
    var fl = $("#fromStockLocation").val();                 // From Location
    var token = $('input[name="_token"]').attr('value');    // X-CSRF token

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
        if (r.status === 'success') {
          //* Do the normal thing here, all requested stock available
          // console.log(r);

          $('#mBomAssembly').modal('hide');     // Hide Modal
          updateInfoWindow('bom', ids[ids.length - 1]); // Update BOM info window with last BOM ID in array
          //TODO: Also select in table
        }
        else if (r.status === 'permission_requested') {
          //* User permission required
          // console.log(r);

          // Display warning and missing stock table
          $('#btnAssembleBOMs').attr('disabled', true);  // Disable main "Assemble" button of modal
          var message = "<div class='alert alert-warning'>There is not enough stock available for " + r.negative_stock.length + " parts. Do you want to continue anyway?<br>";
          message += "<div style='text-align:right;'><button type='button' class='btn btn-secondary btn-sm' data-bs-dismiss='modal' id='btnCancelAnywayAssembly'>Cancel</button> <button type='submit' class='btn btn-primary btn-sm' id='btnAssembleBOMsAnyway'>Do It Anyway</button></div></div>"
          message += r.negative_stock_table;
          $('#mBomAssemblyInfo').html(message);

          // Attach click listener to "Do It Anyway" button
          $('#btnAssembleBOMsAnyway').on('click', function () {
            //TODO: Passing ids for updating table after success but this won't work in the future for selectively updating
            continueAnyway(r, ids, token);
          });

          // Attach click listener to "Cancel" button next to the "Do It Anyway" button
          $('#btnCancelAnywayAssembly').on('click', function () {
            // Hide modal and perform cleanup operations
            hideBOMAssemblyModalAndCleanup();
          });
        }
        removeClickListeners('#btnAssembleBOMs'); // Remove click listener assembly
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


/*
* Hides BOM Assembly modal and cleans all info and click listeners from it
*/
function hideBOMAssemblyModalAndCleanup() {
  // Empty form
  $('#bomAssemblyForm')[0].reset();
  $('#mBomAssemblyInfo').empty();
  $('#btnAssembleBOMs').attr('disabled', false);
  // Hide modal
  $('#mBomAssembly').modal('hide');
  // Remove click listeners
  removeClickListeners('#btnAssembleBOMs'); // Remove click listener assembly
  removeClickListeners('#btnAssembleBOMsAnyway'); // Remove click listener assembly
  // Dispose modal after it's hidden
  $('#mBomAssembly').on('hidden.bs.modal', function (e) {
    $(this).modal('dispose');
  });
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
      // console.log(response);
      $('#mBomAssembly').on('hidden.bs.modal', function (e) {
        $('#bomAssemblyForm')[0].reset();
        $('#mBomAssemblyInfo').empty();
        $('#btnAssembleBOMs').attr('disabled', false);
        $(this).modal('dispose');
      }).modal('hide');
      updateInfoWindow('bom', ids[ids.length - 1]) // Update BOM info with last BOM ID in array
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