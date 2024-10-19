export { SupplierRowManager };
import { DropdownManager } from "./DropdownManager";
import { updateInfoWindow } from "./custom";

/**
 * This class manages the supplier data rows in the parts info window
 */
class SupplierRowManager {
    constructor() {
        this.newRowIndex = 0;
        this.dropdownManager = new DropdownManager();

        // Attach a click listener to remove row buttons
        this.removeRowButtonClickListener();
    }

    /**
     * Adds a new supplier data row to a specific table
     * 
     * @param {string} tableId - The ID of the table to add the row to.
     * @param {number} partId - The ID of the part (optional).
     */
    addSupplierRow(tableId, partId) {
        let newRowIndex = this.newRowIndex;
        let newDropdownDiv = `addPartSupplier-${newRowIndex}`;

        // Check if the table requires extra fields (specific to #partSupplierDataTable)
        let selectBox = tableId === '#partSupplierDataTable' ? '<td></td>' : '';
        let createBox = tableId === '#partSupplierDataTable' ? `<button type="button" class="btn btn-sm btn-success ms-1" id="create-${newRowIndex}"><i class="fas fa-check"></i></button>` : '';

        // Create the new row with a unique dropdown ID for each row
        let newRow = `<tr data-supplier-index="${newRowIndex}" data-part-id="${partId}">
                    ${selectBox}
                    <td><div id='${newDropdownDiv}'></div></td>
                    <td><input type='text' class='form-control form-control-sm' placeholder='URL' data-url-id="${newRowIndex}"></td>
                    <td><input type='text' class='form-control form-control-sm' placeholder='SPN' data-spn-id="${newRowIndex}"></td>
                    <td><input type='text' class='form-control form-control-sm' placeholder='Price' data-price-id="${newRowIndex}"></td>
                    <td><div class='d-flex'><button type="button" class="btn btn-sm btn-danger remove-row-btn"><i class="fas fa-trash"></i></button>${createBox}</div></td>
                  </tr>`;

        // Append the new row to the specified table body
        $(`${tableId} tbody`).append(newRow);

        // Fetch suppliers and populate the dropdown
        this.getSuppliers().done((suppliers) => {
            this.dropdownManager.addPartSupplierDropdown(suppliers, newDropdownDiv, newRowIndex);
        });

        // Attach a save Supplier Row handler if the table is #partSupplierDataTable
        if (tableId === '#partSupplierDataTable') {
            this.saveSupplierDataRowButtonClickListener(`create-${newRowIndex}`);
        }
        this.newRowIndex++;
    }

    /**
     * Attaches an event listener for adding a supplier data row when a button is clicked.
     * 
     * @param {string} tableId - The ID of the table to add the row to.
     * @param {string} buttonId - The ID of the button that triggers row addition.
     * @param {number|null} partId - The ID of the part (optional).
     */
    addSupplierDataRowButtonClickListener(tableId, buttonId, partId = null) {
        $(`#${buttonId}`).off('click').on('click', () => {
            this.addSupplierRow(tableId, partId);
        });
    }

    /**
     * Attaches a click listener to remove the row.
     */
    removeRowButtonClickListener() {
        $(document).on('click', '.remove-row-btn', function () {
            $(this).closest('tr').remove();
        });
    }

    /**
     * Attaches an event listener for saving a supplier data row.
     * 
     * @param {string} buttonId - The ID of the button that triggers saving the row.
     */
    saveSupplierDataRowButtonClickListener(buttonId) {
        let $button = `#${buttonId}`;
        $($button).off('click').on('click', () => {
            this.saveSupplierDataRow($button);
        });
    }

    /**
     * Sends the supplier data from the new row to the server.
     * 
     * @param {jQuery} button - The button that triggered the save action.
     */
    saveSupplierDataRow(button) {
        // Get the new supplier data for this row
        let newSupplierData = this.getNewSupplierDataRowData(button);

        // If there's supplier data
        if (newSupplierData.length > 0) {
            let part_id = newSupplierData[0].part_id;

            // Prepare the data to be sent to the server
            let requestData = {
                part_id: part_id,
                type: 'supplier_data',
                suppliers: newSupplierData.map(supplierRow => ({
                    supplier_id: supplierRow.supplier_id,
                    URL: supplierRow.URL,
                    SPN: supplierRow.SPN,
                    price: supplierRow.price
                }))
            };

            // Send the AJAX request to save supplier data
            $.ajax({
                url: '/supplierData.create',
                type: 'POST',
                data: requestData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    console.log('Supplier data saved successfully:', response);
                    updateInfoWindow('part', part_id);  // Update the InfoWindow after saving
                },
                error: function (xhr) {
                    if (xhr.status === 403) {
                        const response = JSON.parse(xhr.responseText);
                        alert(response.message);
                    } else {
                        console.error('Error saving supplier data:', xhr);
                    }
                }
            });
        } else {
            console.log('No supplier data to save');
        }
    }

    /**
     * Collects the supplier data from the new row in the table.
     * 
     * @param {jQuery} button - The button that triggered the save action.
     * @returns {Array} newSupplierData - Array containing supplier data for the new row.
     */
    getNewSupplierDataRowData(button) {
        let newSupplierData = [];
        let $row = $(button).closest('tr');  // Find the closest row to the button clicked

        // Get the supplier index from the current row
        let rowIndex = $row.data('supplier-index');
        if (typeof rowIndex !== 'undefined') {
            // Collect the data for the current row
            let supplierRow = {
                part_id: $row.data('part-id'),
                supplier_id: $row.find(`[data-supplier-id="${rowIndex}"]`).val(),
                URL: $row.find(`[data-url-id="${rowIndex}"]`).val(),
                SPN: $row.find(`[data-spn-id="${rowIndex}"]`).val(),
                price: $row.find(`[data-price-id="${rowIndex}"]`).val()
            };
            newSupplierData.push(supplierRow);
        }
        return newSupplierData;
    }


    bootstrapSupplierDataTable() {
        $('#addSuppliers').on('shown.bs.collapse', event => {

            // Resize partEntry modal upon hiding supplier data table
            $('#mPartEntry').removeClass('modal-lg').addClass('modal-xl');

            // Bootstrap the table only if it isn't bootstrapped yet
            if ($('#supplierDataTable').data('bootstrap.table')) {
                //
            } else {
                // Timeout to wait for the size transformation to happen
                // Otherwise the resizable column handles are not where the columns are
                setTimeout(() => {
                    $('#supplierDataTable').bootstrapTable({
                        formatNoMatches: function () {
                            return '';
                        },
                        resizable: true,
                    });

                    this.addSupplierRow('#supplierDataTable');
                }, 300);
            }
        })
    }

    // Reset all the bootstrap-table and collapse shenanigans in the Supplier Data section of the part entry modal
    resetSupplierDataTableOnModalHide() {
        const $supplierTable = $('#supplierDataTable');
        const $addSuppliers = $('#addSuppliers');
        $('#mPartEntry').on('hidden.bs.modal', () => {
            $supplierTable.bootstrapTable('destroy');  // Destroy bootstrap-table instance
            $addSuppliers.empty();  // Clear the content inside the supplier div

            // Rebuild the supplier data table structure
            $('#addSuppliers').append(`
                <div id="supplierTableContainer">
                    <table id="supplierDataTable" class="table table-sm table-bordered table-hover">
                        <thead>
                            <tr>
                                <th data-field="supplier">Supplier</th>
                                <th data-field="URL">URL</th>
                                <th data-field="SPN">SPN</th>
                                <th data-field="price">Price</th>
                                <th data-field="remove"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Rows will be dynamically added here -->
                        </tbody>
                    </table>
                </div>
                <button type="button" id="addSupplierRowBtn-partEntry" class="btn btn-sm btn-secondary mt-2">Add Supplier</button>
            `);

            // Collapse the supplier data div and reset modal size
            $('#addSuppliers').removeClass('show');
            $('#mPartEntry').removeClass('modal-xl').addClass('modal-lg');
        });
    }

    // Handle resizing the modal when the supplier data table is collapsed
    resizeModalOnSupplierTableCollapse() {
        $('#addSuppliers').on('hidden.bs.collapse', event => {
            $('#mPartEntry').removeClass('modal-xl').addClass('modal-lg');
        });
    }

    /**
     * Fetches the suppliers from the server.
     * 
     * @returns {Promise} - A promise that resolves with the list of suppliers.
     */
    getSuppliers() {
        return $.ajax({
            url: '/suppliers.get',
            dataType: 'json',
            error: function (error) {
                console.error('Error fetching suppliers:', error);
            }
        });
    }
}
