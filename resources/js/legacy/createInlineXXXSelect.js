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