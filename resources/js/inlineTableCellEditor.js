export { InlineTableCellEditor }
import { updateInfoWindow } from "./custom";
import { rebuildPartsTable } from "./tables";

class InlineTableCellEditor {
    constructor(options) {
        // Options
        this.type = options.type;
        this.endpoint = options.endpoint;
        this.$cell = options.$cell;
        this.originalValue = options.originalValue;
        // Changed flag
        this.valueChanged = false;
    }

    editCell() {
        if (this.type === 'text') {
            this.editTextCell();
        } else {
            this.editDropdownCell();
        }
    }

    editTextCell() {
        // Create input field
        const input = $('<textarea class="form-control">').val(this.originalValue);
        this.$cell.empty().append(input);
        input.focus();

        // Create label for input field
        const label = $('<small class="text-muted">Enter: Confirm</small>');
        this.$cell.append(label);

        // Confirm upon pressing Enter key
        input.keypress((event) => {
            if (event.keyCode === 13) {
                input.blur();
            }
        });

        // Close input on "Escape" key press
        input.on('keydown', (event) => {
            if (event.key === "Escape") {
                input.remove();
                this.$cell.text(this.originalValue);
                this.$cell.removeClass('editing');
                return;
            }
        });

        // Enter new value
        input.blur(() => {
            // Get newly entered value
            const new_value = input.val();

            // Update cell with new value
            this.$cell.text(new_value);

            // Get cell id, column name and database table
            // These are encoded in the table data cells
            const id = this.$cell.closest('td').data('id');
            const column = this.$cell.closest('td').data('column');
            const table_name = this.$cell.closest('td').data('table_name');
            const id_field = this.$cell.closest('td').data('id_field');
            console.log(id, id_field, column, table_name, new_value);

            // Call the updating function
            this.updateCell(id, column, table_name, new_value, id_field);
            this.$cell.removeClass('editing');

            //TODO: Not great - but works?!
            if (table_name == 'parts') {
                updateInfoWindow('part', id);
            } else if (table_name == 'locations') {
                updateInfoWindow('location', id);
            } else if (table_name == 'footprints') {
                updateInfoWindow('footprint', id);
            } else if (table_name == 'suppliers') {
                updateInfoWindow('supplier', id);
            } else if (table_name == 'boms') {
                updateInfoWindow('bom', id);
            } else if (table_name == 'part_categories') {
                $('#categories_list_table').treegrid({
                    treeColumn: 1
                });
                //! This does not really work so I just leave it and try to get away with saying it's rare to rename a category
                // rebuildPartsTable('');
            }
        });
    }

    editDropdownCell() {
        $.ajax({
            type: 'GET',
            url: `/${this.endpoint}.get`,
            dataType: 'JSON',
            success: (response) => {
                const data = response;

                // Create select element
                const select = this.createSelectElement(data, this.originalValue, `${this.type}_name`, `${this.type}_id`);

                // Append, selectize category dropdown
                this.$cell.empty().append(select);
                select.selectize();

                // Need to focus the selectize control
                const selectizeControl = select[0].selectize;
                selectizeControl.focus();

                // Select element change event handler and callback function to set flag
                selectizeControl.on('change', () => {
                    this.selectEventHandler(select, this.$cell, data, () => {
                        this.valueChanged = true;
                    });
                });

                selectizeControl.on('dropdown_close', () => {
                    this.selectEventHandler(select, this.$cell, data, () => {
                        this.valueChanged = true;
                    });
                });

                // Listen for the blur event on the selectize control
                selectizeControl.on('blur', () => {
                    // Remove the select element when the selectize dropdown loses focus
                    select.remove();
                    // Change cell text back if value was not changed
                    if (!this.valueChanged) {
                        this.$cell.text(this.originalValue);
                    }
                    this.$cell.removeClass('editing');
                });

                // Listen for the Escape keydown event on the document level
                $(document).on('keydown', (event) => {
                    if (event.key === "Escape" && this.$cell.hasClass('editable') && this.$cell.hasClass(this.type) && this.$cell.hasClass('editing')) {
                        select.remove();
                        // Change cell text back if value was not changed
                        if (!this.valueChanged) {
                            this.$cell.text(this.originalValue);
                        }
                        this.$cell.removeClass('editing');
                        // Remove the event handler once it has done its job
                        $(document).off('keydown');
                    }
                });
            },
            error: function (error) {
                console.log('Error:', error);
            }
        });
    }

    /**
     * Create a select element with given data and current value
     * @param {Array} data - Array of data objects
     * @param {string} currentValue - Current text value of the table cell that is edited
     * @param {string} textKey - The key for the text to display in the options
     * @param {string} valueKey - The key for the value of the options
     * @returns {jQuery} - The created select element
     */
    createSelectElement(data, currentValue, textKey, valueKey) {
        // New select element
        const select = $('<select class="form-select-sm">');
        // Iterate over all available data
        data.forEach(item => {
            // Create new option
            const option = $('<option>').text(item[textKey]).attr('value', item[valueKey]);
            if (item[textKey] === currentValue) {
                option.attr('selected', true);
            }
            select.append(option);
        });
        return select;
    }

    /**
     * Handles the event when the select element's value changes
     * @param {jQuery} select - The select element
     * @param {jQuery} cell - The table cell being edited
     * @param {Array} data - Array of data objects
     * @param {Function} changeFlagCallback - Callback function to set change flag
     */
    selectEventHandler(select, cell, data, changeFlagCallback) {
        const selectedValue = select.val(); // Get new selected value

        // Get cell part_id, column name and database table
        // These are encoded in the table data cells
        const id = cell.closest('td').data('id');
        const column = `part_${this.type}_fk`;
        const table_name = cell.closest('td').data('table_name');
        const id_field = cell.closest('td').data('id_field');

        // Call the database table updating function
        $.when(this.updateCell(id, column, table_name, selectedValue, id_field)).done(() => {
            // Find the name for a given ID
            const newValue = data.find(item => item[`${this.type}_id`] === parseInt(selectedValue));
            // Check if newValue is found and update HTML cell
            if (newValue) {
                cell.text(newValue[`${this.type}_name`]); // Get the name from the found item
            } else {
                console.log(`No matching ${this.type} found for id:`, selectedValue);
            }
            // Editing aftermath
            select.remove();
            cell.removeClass('editing');
            changeFlagCallback(); // Callback function to set change flag
            $(document).off('keydown'); // Removing the escape handler because it's on document level
        });
    }

    /**
     * Updates a cell value in the database using AJAX.
     * @param {number} id - The ID of the row containing the cell to be updated.
     * @param {string} column - The name of the column containing the cell to be updated.
     * @param {string} table_name - The name of the database table containing the cell to be updated.
     * @param {string} new_value - The new value to be assigned to the cell.
     * @param {string} id_field - The name of the primary key field in the database table.
     * @returns {object} - A jQuery AJAX object that can be used to handle the success and error events of the request.
     */
    updateCell(id, column, table_name, new_value, id_field) {
        const token = $('input[name="_token"]').attr('value');
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
                // Successfully updated data
            },
            error: function (xhr) {
                if (xhr.status === 419) {
                    alert('CSRF token mismatch. Please refresh the page and try again.');
                } else {
                    alert('Error updating data');
                }
            }
        });
    }
}