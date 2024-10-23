export { PartCreator };
import { ResourceCreator } from "./ResourceCreator";
import { SupplierRowManager } from "../../../Tables/SupplierRowManager";
import { DataFetchService } from "../DataFetchService";

class PartCreator extends ResourceCreator {
    constructor(options, tableRebuildFunctions = []) {
        super(options, tableRebuildFunctions);
        this.initializeUppercaseToggle();
        this.togglePartEntryButtons();
        this.togglePartInputs();
        this.supplierRowManager = new SupplierRowManager();
        this.initializeAddStockToggler();
    }

    // Collect form data and add supplier info
    collectFormData() {
        super.collectFormData();
        this.data['suppliers'] = this.collectSupplierData();
    }

    // Populate dropdowns and add additional event listeners
    populateDropdowns(data) {
        const [locations, footprints, categories] = data;
        this.dropdownManager.addPartLocationDropdown(locations);
        this.dropdownManager.addPartFootprintDropdown(footprints);
        if (!this.dropdownManager.categoryCreated) {
            this.dropdownManager.addPartCategoryDropdown(categories);
        }
        this.dropdownManager.categoryCreated = false;
        this.supplierRowManager.addSupplierDataRowButtonClickListener('#supplierDataTable', 'addSupplierRowBtn-partEntry');
    }

    // Fetch data required for dropdowns (locations, footprints, categories)
    fetchDropdownData() {
        return [DataFetchService.getLocations(), DataFetchService.getFootprints(), DataFetchService.getCategories()];
    }

    rebuildTables(id) {
        super.rebuildTables(id);
        this.tableManager.updateStockModal(id); // Adds the part name to the stock modal
    }

    onModalHidden(event) {
        super.onModalHidden(event);
        console.log("hidden this.inputModal");

        // Check if the part entry modal was hidden because the category creation modal came into view
        if (this.partModalHiddenByCategoryModal(event)) {
            console.log("Part modal hidden by category modal");
            $('#mouserSearchResults').empty();
        }

        if (!this.skipDropdownPopulation) {
            $('#addPartAddStockSwitch').prop('checked', false).trigger('change');
        }
    }

    partModalHiddenByCategoryModal(event) {
        console.log("hiding element = ", event.target.id);
        return event.target !== this.inputModal[0];
    }

    onModalShow() {
        super.onModalShow();

        // Conditionally fetch and populate dropdown data
        if (!this.skipDropdownPopulation) {
            let dataFetchPromises = this.fetchDropdownData();

            Promise.all(dataFetchPromises)
                .then(data => {
                    if (data.length > 0) {
                        // Populate dropdowns
                        this.populateDropdowns(data);
                    }
                })
                .catch(error => console.error('Error fetching dropdown data:', error));
        }
        this.skipDropdownPopulation = false; // Reset flag
    }





    // Collect data from supplier rows in the part form
    collectSupplierData() {
        let suppliers = [];
        $('#supplierDataTable tbody tr').each(function () {
            let rowIndex = $(this).data('supplier-index');
            let supplierRow = {
                supplier_id: $(`[data-supplier-id="${rowIndex}"]`).val(),
                URL: $(`[data-url-id="${rowIndex}"]`).val(),
                SPN: $(`[data-spn-id="${rowIndex}"]`).val(),
                price: $(`[data-price-id="${rowIndex}"]`).val()
            };
            suppliers.push(supplierRow);
        });
        return suppliers;
    }

    // Initialize uppercase toggle for part name input field
    initializeUppercaseToggle() {
        const $toggleButton = $('#toggle-uppercase-button');
        const $addPartName = $('#addPartName');
        let isUppercase = false;
        let originalValue = '';

        const toggleUppercase = () => {
            isUppercase = !isUppercase;
            if (isUppercase) {
                originalValue = $addPartName.val(); // Store original value before converting to uppercase
                $addPartName.on('input.uppercase', function () {
                    const uppercased = $(this).val().toUpperCase();
                    $(this).val(uppercased);
                });
                $addPartName.val($addPartName.val().toUpperCase());
                $toggleButton.addClass('active'); // Indicate active state
                $toggleButton.text('AA');
            } else {
                $addPartName.off('input.uppercase'); // Remove event listener for uppercase
                $addPartName.val(originalValue); // Restore original value
                $toggleButton.removeClass('active'); // Remove active state indication
                $toggleButton.text('Aa');
            }
        };

        // Ensure no duplicate event listeners are attached
        $toggleButton.off('click');
        $toggleButton.click(() => {
            toggleUppercase();
        });

        $toggleButton.text('Aa'); // Initialize button text
    }

    // Toggle the "Add Stock" functionality for a new part
    initializeAddStockToggler() {
        $('#addPartAddStockSwitch').off('change').on('change', function () {
            $('#addPartQuantity').prop('disabled', !this.checked);

            var selectizeControl = $('#addPartLocSelect')[0].selectize;

            if (this.checked) {
                selectizeControl.enable();
            } else {
                selectizeControl.disable();
                $('#addPartQuantity').val('');
            }
        });
    }

    handleSuccess(response) {
        super.handleSuccess(response);
        this.supplierRowManager.resetSupplierDataTable();
        this.resetPartEntryButtons();

        // Reset flags
        this.skipDropdownPopulation = false;
        this.dropdownManager.categoryCreated = false;
    }

    togglePartEntryButtons() {
        // Highlight the "Suppliers" button when the suppliers section is toggled
        $('#addSuppliers').on('show.bs.collapse', function () {
            $('#showSuppliers').addClass('active');
        }).on('hide.bs.collapse', function () {
            $('#showSuppliers').removeClass('active');
        });

        // Highlight the "Additional Info" button when the advanced options section is toggled
        $('#advancedOptions').on('show.bs.collapse', function () {
            $('#showAdvanced').addClass('active');
        }).on('hide.bs.collapse', function () {
            $('#showAdvanced').removeClass('active');
        });
    }

    resetPartEntryButtons() {
        $('#showSuppliers').removeClass('active');
        $('#showAdvanced').removeClass('active')
    }

    togglePartInputs() {
        // Initially show Manual Entry and hide Mouser Search
        $('#manualEntrySection').show();
        $('#mouserSearchSection').hide();

        // Manual Entry Button Click Event
        $('#manualEntryButton').on('click', function () {
            $('#manualEntrySection').show();
            $('#mouserSearchSection').hide();
            // Optionally mark the active button
            $(this).addClass('active');
            $('#mouserSearchButton').removeClass('active');
            $('#addPartName').focus();
        });

        // Mouser Search Button Click Event
        $('#mouserSearchButton').on('click', function () {
            $('#manualEntrySection').hide();
            $('#mouserSearchSection').show();
            // Optionally mark the active button
            $(this).addClass('active');
            $('#manualEntryButton').removeClass('active');
            $('#mouserPartName').focus();
        });
    }
}