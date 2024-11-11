export { InlineTableCellEditor };
import { updateInfoWindow } from "../custom";
import { CategoryTableManager } from "./CategoriesTableManager";
import { TableManager } from "./TableManager";
import { TableRowManager } from "./TableRowManager";

class InlineTableCellEditor {
    constructor() {
        this.type = null;
        this.$cell = null;
        this.$cellContent = null;
        this.originalValue = null;
        this.originTable = null;
        this.valueChanged = false;
        this.table = null;
    }

    enableInlineProcessing() {
        $(document).off('click', '.edit-pen');
        $(document).on('click', '.edit-pen', (event) => {
            event.preventDefault();
            const cell = $(event.currentTarget).closest('.editable');
            if (cell.hasClass('editing')) {
                return;
            }

            // Extract the type from the class that matches the pattern "editable-type"
            const editableClass = cell.attr('class').split(' ').find(cls => cls.startsWith('editable-'));
            const type = editableClass ? editableClass.replace('editable-', '') : null;

            if (!type) {
                console.error('Editable type not found for the clicked cell.');
                return;
            }

            // Set instance-level properties based on the clicked cell
            this.type = type;
            this.$cell = cell;
            this.$cellContent = this.$cell.find('.d-flex #contentSpan');

            // Find the content within the flexbox component
            this.originalValue = this.$cellContent.text().trim();
            this.originTable = cell.closest('table').attr('id');
            this.valueChanged = false;

            // Set endpoint based on type
            this.setEndpoint();

            cell.addClass('editing');
            this.$cellContent.addClass('me-auto flex-grow-1');
            this.editCell();
        });
    }

    setEndpoint() {
        this.table = 'partsTable';
        switch (this.type) {
            case 'category':
                this.endpoint = 'categories';
                break;
            case 'footprint':
                this.endpoint = 'footprints';
                break;
            case 'supplier':
                this.endpoint = 'suppliers';
                break;
            case 'supplierData':
                this.type = 'supplier';         // Need this change, otherwise the way that editDropdownCell calls createSelectElement doesn't work properly
                this.endpoint = 'suppliers';
                this.table = 'supplier_data';
                break;
            case 'text':
                break;
            default:
                console.error(`Unknown type: ${this.type}`);
                break;
        }
    }

    editCell() {
        if (this.type === 'text') {
            this.editTextCell();
        } else {
            this.editDropdownCell();
        }
    }

    editTextCell() {
        // Create input field for changing the value
        const input = $('<textarea class="form-control">').val(this.originalValue.trim());

        // Find the content span within the table cell
        this.$cellContent.empty().append(input);
        input.focus();

        // Create label for input field
        const label = $('<small class="text-muted" id="enter-helper">Enter: Confirm</small>');
        this.$cell.append(label);

        // Update database value on pressing Enter key
        input.keypress((event) => {
            if (event.keyCode === 13) {
                input.blur();
            }
        });

        // Update database value on blur event (clicking outside the input field)
        input.blur(() => {
            // Get newly entered value
            const new_value = input.val();

            // Update cell with new value
            this.$cellContent.text(new_value);

            // Get database row id, id column name, currently edited column name and database table
            // These are encoded in the table data cells and look like this, e.g.:
            // 337 'part_id' 'part_description' 'parts'
            const id = this.$cell.closest('td').data('id');
            const column = this.$cell.closest('td').data('column');
            const table_name = this.$cell.closest('td').data('table_name');
            const id_field = this.$cell.closest('td').data('id_field');

            // Call the updating function
            this.updateCell(id, column, table_name, new_value, id_field);
            this.$cell.find('.text-muted').remove();
            this.$cell.removeClass('editing');

            this.refreshTableAndInfoWindows(table_name, id)
            this.enableInlineProcessing();
        });

        // Close input on "Escape" key press (don't update)
        input.on('keydown', (event) => {
            if (event.key === "Escape") {
                input.remove();
                this.$cellContent.text(this.originalValue);
                this.$cell.find('.text-muted').remove();
                this.$cell.removeClass('editing');
                return;
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

                //TODO: Dropdownmanager here
                // Create select element
                const select = this.createSelectElement(data, this.originalValue, `${this.type}_name`, `${this.type}_id`);

                // Append, selectize category dropdown and make sure it fills the whole cell width
                this.$cellContent.empty().append(select);
                select.selectize({
                    onDropdownOpen: function () {
                        $('.bootstrap-table .fixed-table-container .fixed-table-body').css({
                            'overflow-x': 'visible',
                            'overflow-y': 'visible'
                        });
                    },
                });

                // Need to focus the selectize control
                const selectizeControl = select[0].selectize;
                selectizeControl.focus();

                // Select element change event handler and callback function to set flag
                // Selectize.js does not natively support listening to multiple events
                // This covers changing the value or chosing a value for the first time
                selectizeControl.on('change', () => {
                    this.selectEventHandler(select, this.$cellContent, data, () => {
                        this.valueChanged = true;
                    });
                });

                // Commenting out this section again because it ALWAYS selects something
                // selectizeControl.on('dropdown_close', () => {
                //     this.selectEventHandler(select, this.$cell, data, () => {
                //         this.valueChanged = true;
                //     });
                // });

                // When done selecting
                selectizeControl.on('blur', () => {
                    // Remove the select element when the selectize dropdown loses focus
                    select.remove();
                    // Change cell text back if value was not changed
                    if (!this.valueChanged) {
                        this.$cellContent.text(this.originalValue);
                    }
                    this.$cell.removeClass('editing');
                    this.enableInlineProcessing();
                });

                // Listen for the Escape keydown event on the document level
                $(document).on('keydown', (event) => {
                    if (event.key === "Escape" && this.$cell.hasClass('editable') && this.$cell.hasClass(this.type) && this.$cell.hasClass('editing')) {
                        select.remove();
                        // Change cell text back if value was not changed
                        if (!this.valueChanged) {
                            this.$cellContent.text(this.originalValue);
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
        const select = $('<select class="form-select-sm w-100">');
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
            const newValue = data.find(item => item[`${this.type}_id`] == parseInt(selectedValue));
            // Check if newValue is found and update HTML cell
            if (newValue) {
                cell.text(newValue[`${this.type}_name`]); // Get the name from the found item
            } else {
                // Do not get confused. Since I listen to two types of events, one never finds a matching ID
                // console.log(`No matching ${this.type} found for id:`, selectedValue);
            }

            // Editing aftermath
            select.remove();
            cell.removeClass('editing');
            changeFlagCallback(); // Callback function to set change flag

            this.refreshTableAndInfoWindows(table_name, id)
            this.enableInlineProcessing();

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

        // Check if all required variables are present, because of the two selectize behaviour
        // One is usually 'empty' and has no new_value -> that throws an error then
        if (!id || !column || !table_name || !new_value || !id_field) {
            console.error('Missing parameters for updateCell:', { id, column, table_name, new_value, id_field });
            return $.Deferred().reject();  // Return a rejected promise
        }

        // Special case for supplier_data
        if (this.table === 'supplier_data') {
            column = 'supplier_id_fk';
        }

        return $.ajax({
            url: '/updateCell',
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
            success: (data) => {
                // Successfully updated data
            },
            error: (xhr) => {
                if (xhr.status === 419) {
                    alert('CSRF token mismatch. Please refresh the page and try again.');
                } else {
                    alert('Error updating data');
                    this.$cell.text(this.originalValue);
                }
            }
        });
    }

    refreshTableAndInfoWindows(table_name, id) {
        // loadSelectedRow method also updates InfoWindow
        const tableManagerMapping = {
            'parts': () => {
                new TableManager({ type: 'part' }).rebuildTable().done(() => {
                    const tableRowManager = new TableRowManager('#parts_table', 'part');
                    tableRowManager.loadSelectedRow();
                });
            },
            'locations': () => {
                new TableManager({ type: 'location' }).rebuildTable().done(() => {
                    const tableRowManager = new TableRowManager('#locations_list_table', 'location');
                    tableRowManager.loadSelectedRow();
                });
            },
            'footprints': () => {
                new TableManager({ type: 'footprint' }).rebuildTable().done(() => {
                    const tableRowManager = new TableRowManager('#footprints_list_table', 'footprint');
                    tableRowManager.loadSelectedRow();
                });
            },

            'suppliers': () => updateInfoWindow('supplier', id),
            'boms': () => updateInfoWindow('bom', id),
            'part_categories': () => {
                new CategoryTableManager({ type: 'category' }).rebuildTable();
                new TableManager({ type: 'part' }).rebuildTable();
            }
        };
        if (tableManagerMapping[table_name]) {
            tableManagerMapping[table_name]();
        }
    }

}