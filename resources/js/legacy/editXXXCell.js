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
        // Selective does not support listening to both events at the same time unfortunately
        selectizeControl.on('change', function () {
          inlineCategorySelectEventHandler(select, cell, categories, function changeFlagCallback() {
            valueChanged = true;
          })
        });
  
        selectizeControl.on('dropdown_close', function () {
          inlineCategorySelectEventHandler(select, cell, categories, function changeFlagCallback() {
            valueChanged = true;
          })
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
        // Selective does not support listening to both events at the same time unfortunately
        selectizeControl.on('change', function () {
          inlineFootprintSelectEventHandler(select, cell, footprints, function changeFlagCallback() {
            valueChanged = true;
          })
        });
  
        selectizeControl.on('dropdown_close', function () {
          inlineFootprintSelectEventHandler(select, cell, footprints, function changeFlagCallback() {
            valueChanged = true;
          })
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
        // Selective does not support listening to both events at the same time unfortunately
        selectizeControl.on('change', function () {
          inlineSupplierSelectEventHandler(select, cell, suppliers, function changeFlagCallback() {
            valueChanged = true;
          })
        });
  
        selectizeControl.on('dropdown_close', function () {
          inlineSupplierSelectEventHandler(select, cell, suppliers, function changeFlagCallback() {
            valueChanged = true;
          })
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