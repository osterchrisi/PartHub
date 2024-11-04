export { StockManager };
import { FormValidator } from '../FormValidator';

import {
    removeClickListeners,
    updateInfoWindow
} from '../custom';

class StockManager {
    constructor() {
        this.token = $('input[name="_token"]').attr('value');
        this.change = null;
        this.pid = null;

        this.$form = $('#stockChangingForm');
        this.formValidator = new FormValidator(this.$form, {
            button: $('#AddStock'),
            submitCallback: this.prepareStockChange.bind(this)
        });
    }

    /**
     * Gets the variables to prepare the stock changing array and sends it to the Parts Controller
     * @param change - The type of change. '1' for adding, '-1' for reducing and '0' for moving stock
     * @param {number} pid - The part ID for the stock change
     */
    prepareStockChange() {
        const q = $("#addStockQuantity").val();       // Quantity
        const c = $("#addStockDescription").val();    // Comment
        let tl = null;
        let fl = null;

        // Get required locations
        if (this.change == '1') {
            tl = $("#toStockLocation").val();   // To Location
        }
        if (this.change == '-1') {
            fl = $("#fromStockLocation").val(); // From Location
        }
        if (this.change == '0') {
            tl = $("#toStockLocation").val();   // To Location
            fl = $("#fromStockLocation").val(); // From Location
        }

        // Prepare stock changes array
        const stockChanges = [{
            quantity: q,
            to_location: tl,
            from_location: fl,
            comment: c,
            part_id: this.pid,
            change: this.change
        }];

        // To and From location are identical - inform user
        if (this.change == '0' && tl == fl) {
            const message = '<div class="alert alert-warning text-center">To and From location are identical</div>';
            $('#mStockModalInfo').html(message);
            return;
        }

        // Call the stock changing script
        this.requestStockChange(stockChanges);
    }

    /**
     * Makes an AJAX call to the stock changing script. If stock change is supported by available stock, update part info window.
     * If there is stock shortage, display a message and request user permission.
     * 
     * @param {Array} stockChanges - Array containing all parameters necessary for the requested stock change
     * @param {number} pid - The part ID for which the stock is changes and later the info window updated
     * @return void
     */
    requestStockChange(stockChanges) {
        $.ajax({
            url: '/parts.requestStockChange',
            type: 'POST',
            data: { stock_changes: stockChanges },
            headers: {
                'X-CSRF-TOKEN': this.token
            },
            success: (response) => {
                const r = response;
                if (r.status === 'success') {
                    //* Do the normal thing here, all requested stock available
                    updateInfoWindow('part', this.pid);
                    // Update 'Total Stock' in parts table
                    const new_stock_level = r.result[r.result.length - 1].new_total_stock;
                    const $cell = $('tr.selected-last td[data-column="total_stock"]');
                    $cell.text(new_stock_level);
                    //TODO: Add a $table.bootstrapTable('updateCell', {index, field, value}) here to keep the data between pagination jumps
                    $('#mAddStock').modal('hide');
                } else {
                    //* User permission required
                    // Display warning and missing stock table
                    $('#AddStock').attr('disabled', true);
                    let message = `<div class='alert alert-warning'>There is not enough stock available for ${r.negative_stock.length} part(s). Do you want to continue anyway?<br>`;
                    message += "<div style='text-align:right;'><button type='button' class='btn btn-secondary btn-sm' data-bs-dismiss='modal'>Cancel</button> <button type='submit' class='btn btn-primary btn-sm' id='btnChangeStockAnyway'>Do It Anyway</button></div></div>";
                    message += r.negative_stock_table;
                    $('#mStockModalInfo').html(message);

                    // Attach click listener to "Do It Anyway" button
                    this.changeStockAnywayClickListener(r, this.pid);
                }
            },
            error: (xhr) => {
                // Handle the error
                if (xhr.status === 419) {
                    // Token mismatch error
                    alert('CSRF token mismatch. Please refresh the page and try again.');
                } else {
                    // Other errors
                    this.formValidator.handleError(xhr);
                }
            }
        });
    }

    /**
     * Changes the status of all requested changes to 'gtg' (good to go).
     * Attaches a click listener to the 'Do It Anyway' button and makes an AJAX call to the Parts Controller (again) with the
     * requested stock changes now all set to 'gtg'.
     * 
     * @param {Array} r - The response of the stock changing script containing the initially requested changes with one of two statuses:
     * - 'gtg': Good to go
     * - 'permission_required': User permission required
     * @param {number} pid - The part ID for which the stock change was requested and the info window will be updated
     * @return void
     */
    changeStockAnywayClickListener(r) {
        $('#btnChangeStockAnyway').off('click').on('click', () => {
            for (const change of r.changes) {
                change.status = 'gtg';
            }

            // Request stock changes from Parts Controller
            $.ajax({
                url: '/parts.requestStockChange',
                type: 'POST',
                data: { stock_changes: r.changes },
                headers: {
                    'X-CSRF-TOKEN': this.token
                },
                success: (response) => {
                    $('#mAddStock').modal('hide');
                    updateInfoWindow('part', this.pid);
                },
                error: (xhr) => {
                    // Handle the error
                    if (xhr.status === 419) {
                        // Token mismatch error
                        alert('CSRF token mismatch. Please refresh the page and try again.');
                    } else {
                        // Other errors
                        alert('An error occurred. Please try again.');
                    }
                }
            });
        });
    }

    /**
    * Show the stock modal and change text according to type of change.
    * Then generate location dropdown menus and selectize them.
    * Finally remove old click listener and attach new one to the 'Save Changes' button
    * 
    * @param {number} change - The type of change. '1' for adding, '-1' for reducing and '0' for moving stock
    * @param {Array} locations - An array of objects containing location information
    * @param {number} pid - The part ID for which to call the stock modal for
    */
    showStockChangeModal(change, locations, pid) {
        let modalTitle = '';
        let changeText = '';
        this.change = change;
        this.pid = pid;

        switch (change) {
            case 1:
                modalTitle = 'Add Stock';
                changeText = 'Add stock to ';
                $('#FromStockLocationDiv-row').hide();
                this.toStockLocationDropdown("ToStockLocationDiv", locations);
                break;
            case -1:
                modalTitle = 'Reduce Stock';
                changeText = 'Reduce stock of ';
                $('#ToStockLocationDiv-row').hide();
                this.fromStockLocationDropdown("FromStockLocationDiv", locations);
                break;
            case 0:
                modalTitle = 'Move Stock';
                changeText = 'Move stock of ';
                this.toStockLocationDropdown("ToStockLocationDiv", locations);
                this.fromStockLocationDropdown("FromStockLocationDiv", locations);
                break;
        }

        document.getElementById('stockModalTitle').textContent = modalTitle;
        document.getElementById('stockChangeText').textContent = changeText;

        $('#mAddStock').modal('show'); // Show modal
        removeClickListeners('#AddStock'); // Remove previously added click listener
        this.formValidator.attachValidation();
    }

    /**
     * Create a dropdown element and selectize it
     * 
     * @param {string} divId The div element in which the element will be created
     * @param {Array} locations An array of associative arrays containing locations
     * @param {string} label The label for the dropdown
     * @param {string} selectId The id for the select element
     * 
     * @return void
     */
    createDropdown(divId, locations, label, selectId) {
        const div = document.getElementById(divId);
        let selectHTML = `<label class='input-group-text' for='${selectId}'>${label}</label><select class='form-select' id='${selectId}'>`;
        locations.forEach(location => {
            selectHTML += `<option value='${location.location_id}'>${location.location_name}</option>`;
        });
        selectHTML += '</select>';
        div.innerHTML = selectHTML;
        $(`#${selectId}`).selectize();
    }

    /**
     * Create the "To Location" dropdown
     * 
     * @param {string} divId The div element in which the element will be created
     * @param {Array} locations An array of associative arrays containing locations
     * 
     * @return void
     */
    toStockLocationDropdown(divId, locations) {
        this.createDropdown(divId, locations, 'To', 'toStockLocation');
    }

    /**
     * Create the "From Location" dropdown
     * 
     * @param {string} divId The div element in which the element will be created
     * @param {Array} locations An array of associative arrays containing locations
     * 
     * @return void
     */
    fromStockLocationDropdown(divId, locations) {
        this.createDropdown(divId, locations, 'From', 'fromStockLocation');
    }

    /**
     * Event handler for removing all HTML elements inside "FromStockLocationDiv" and "ToStockLocationDiv" divs when the "mAddStock" modal is hidden.
     * This to keep them from stacking up.
     */
    attachModalHideListener() {
        $('#mAddStock').on('hidden.bs.modal', () => {
            this.emptyDivFromHTML("FromStockLocationDiv");
            this.emptyDivFromHTML("ToStockLocationDiv");
        });
    }

    /**
     * Empties a div from all its HTML elements by its element ID
     * @param {string} id - The div ID 
     * @return void
     */
    emptyDivFromHTML(id) {
        const div = document.getElementById(id);
        div.innerHTML = '';
    }
}