class InlineTableCellEditor {
    constructor(options) {
        // Options
        this.type = options.type,
        this.$cell = options.$cell
        this.originalValue = options.originalValue
    }
    /**
     * Inline table cell manipulation of bootstrapped tables.
     * Is called in the rebuildXXXTable functions to enable inline cell editing.
     * 1) Check if the cell is already being edited
     * 2) Add editing class to the cell
     * 3) Get current value
     * 
     * if (cell.hasClass('category')) {
     *   editCategoryCell(cell, originalValue);
     * }
     * else if (cell.hasClass('footprint')) {
     *   editFootprintCell(cell, originalValue);
     * }
     * else if (cell.hasClass('supplier')) {
     *   editSupplierCell(cell, originalValue);
     * }
     * //* It's a text cell
     * else {
     *   editTextCell(cell, originalValue);
     * }
     */
    inlineProcessing() {
        $('.bootstrap-table').on('dblclick', '.editable', function (e) { });
    };
    // Chooses between:
    // These do:
    // Create, append, selectize a select element
    // Call the correct Event Handler for change and dropdown events on the selective
    // Do something also on blur and keydown events of the selectize element

    // Changed flag
    valueChanged = false;
    editCell() {
        if (this.type == 'text') { 
            // text cell editing
        }
        else {
            $.ajax({
                type: 'GET',
                url: '/${this.type}.get',
                dataType: 'JSON',
                success: function (response) {
                    data = response;

                    // Create select element
                    var select = createSelectElement(data, this.originalValue); //! Have no original value yet

                    // Append, selectize category dropdown
                    cell.empty().append(select);
                    select.selectize();

                    // Need to focus the selectize control
                    var selectizeControl = select[0].selectize;
                    selectizeControl.focus();

                    // Select element change event handler and callback function to set flag
                    // Selective does not support listening to both events at the same time unfortunately
                    selectizeControl.on('change', function () {
                        selectEventHandler(select, cell, data, function changeFlagCallback() {
                            valueChanged = true;
                        })
                    });

                    selectizeControl.on('dropdown_close', function () {
                        selectEventHandler(select, cell, data, function changeFlagCallback() {
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

    }


    editTextCell(cell, originalValue) { };
    // They then call one of these: 
    // These can become one function that just deals with a JSON array
    inlineCategorySelectEventHandler(select, cell, categories, changeFlagCallback) { };
    inlineFootprintSelectEventHandler(select, cell, footprints, changeFlagCallback) { };
    inlineSupplierSelectEventHandler(select, cell, suppliers, changeFlagCallback) { };
    //Essentially gathers a bunch of strings from the HTML table elements and calls:
    /**
     * Updates a cell value in the database using AJAX.
     * @param {number} id - The ID of the row containing the cell to be updated.
     * @param {string} column - The name of the column containing the cell to be updated.
     * @param {string} table_name - The name of the database table containing the cell to be updated.
     * @param {string} new_value - The new value to be assigned to the cell.
     * @param {string} id_field - The name of the primary key field in the database table.
     * @returns {object} - A jQuery AJAX object that can be used to handle the success and error events of the request.
     */
    updateCell(id, column, table_name, new_value, id_field) { };
    // Then calls the changeFlagCallback - seems like it's never called actually...

    // These three are already the same:
    // They appear in the editXXXCell methods above
    appendInlineCategorySelect(cell, select) { }
    appendInlineFootprintSelect(cell, select) { }
    appendInlineSupplierSelect(cell, select) { };
}