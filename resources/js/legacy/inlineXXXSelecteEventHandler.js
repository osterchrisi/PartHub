function inlineCategorySelectEventHandler(select, cell, categories, changeFlagCallback) {
    var selectedValue = select.val(); // Get new selected value
  
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
  }
  
  function inlineFootprintSelectEventHandler(select, cell, footprints, changeFlagCallback) {
    var selectedValue = select.val(); // Get new selected value
  
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
  }
  
  function inlineSupplierSelectEventHandler(select, cell, suppliers, changeFlagCallback) {
    var selectedValue = select.val(); // Get new selected value
  
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
  }