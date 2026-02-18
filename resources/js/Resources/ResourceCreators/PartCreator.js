export { PartCreator };
import { ResourceCreator } from "./ResourceCreator";
import { SupplierRowManager } from "../../Tables/SupplierRowManager";
import { DataFetchService } from "../../Services/DataFetchService";
import { DropdownManager } from "../../DropdownManager";

class PartCreator extends ResourceCreator {
    constructor(options) {
        super(options);
        this.initializeUppercaseToggle();
        this.initializePartEntryButtons();
        this.initializePartInputButtons();
        this.initializeAddStockToggler();
        this.attachCategoryModalCloseListeners();

        //Supplier Row Manager
        this.supplierRowManager = new SupplierRowManager({
            inputForm: '#partEntryForm',
            table: '#supplierDataTable'
        });
        this.supplierRowManager.bootstrapSupplierDataTable();
        this.supplierRowManager.resizeModalOnSupplierTableCollapse();

        // Dropdown Manager
        this.dropdownManager = new DropdownManager({ inputModal: this.inputModal });
        this.skipDropdownPopulation = false;    // Flag to control dropdown population when partEntry modal is closed and re-opened
    }

    // Collect form data and add supplier info
    collectFormData() {
        super.collectFormData();

        const suppliers = this.collectSupplierData();
        if (suppliers === null) return false; // invalid

        this.data['suppliers'] = suppliers;
        return true;
    }


    handleSuccess(response) {
        super.handleSuccess(response);

        // Part entry modal specific thingies
        this.supplierRowManager.resetSupplierDataTable();
        this.resetPartEntryButtons();

        // Reset flags
        this.skipDropdownPopulation = false;
        this.dropdownManager.categoryCreated = false;
    }

    // Populate all dropdowns
    populateAllDropdowns(data) {
        const { locations, footprints, categories } = data; // Object destructuring
        this.dropdownManager.addPartLocationDropdown(locations);
        this.dropdownManager.addPartFootprintDropdown(footprints);
        this.dropdownManager.addPartCategoryDropdown(categories);
    }

    // Fetch data required for dropdowns (locations, footprints, categories)
    fetchAllDropdownData() {
        return Promise.all([
            DataFetchService.getLocations(),
            DataFetchService.getFootprints(),
            DataFetchService.getCategories()
        ]).then(([locations, footprints, categories]) => {
            return { locations, footprints, categories }; // Return object
        });
    }

    rebuildTables(id) {
        super.rebuildTables(id);
        this.tableManager.updateStockModal(id); // Adds the part name to the stock modal
    }

    // Attach listeners to category modal close buttons
    attachCategoryModalCloseListeners() {
        $('#closeCategoryModalButton1, #closeCategoryModalButton2').off('click').on('click', () => {
            this.skipDropdownPopulation = true;  // Upon returning to part entry modal, the dropdowns keep their values
            this.inputModal.modal('toggle');
        });
    }

    onModalHidden(event) {
        super.onModalHidden(event);

        if (!this.skipDropdownPopulation) {
            $('#addPartAddStockSwitch').prop('checked', false).trigger('change');
        }
    }

    onModalShow() {
        super.onModalShow();

        //* Populate all dropdowns (modal gets shown anew)
        if (!this.skipDropdownPopulation) {
            this.fetchAllDropdownData()
                .then(data => {
                    this.populateAllDropdowns(data);
                    this.supplierRowManager.addSupplierDataRowButtonClickListener('addSupplierRowBtn-partEntry');
                })
                .catch(error => console.error('Error fetching dropdown data:', error));
        }

        //* Populate only categories (user came back from NOT creating a new category - otherwise dropdownManager does this)
        if (this.skipDropdownPopulation && !this.dropdownManager.categoryCreated) {
            DataFetchService.getCategories()
                .then(categories => {
                    this.dropdownManager.addPartCategoryDropdown(categories);
                })
                .catch(error => console.error('Error fetching categories:', error));
        }

        // Mouser tab access control based on user settings
        this.syncMouserTabAccess();


        // Reset flags
        this.skipDropdownPopulation = false;
        this.dropdownManager.categoryCreated = false;
    }

    // Collect data from supplier rows in the part entry form
    collectSupplierData() {
        const suppliers = [];
        const errors = {};

        // row order matters because FormValidator maps suppliers.<index>.field by visible row order
        $('#supplierDataTable tbody tr').each(function (i) {
            const rowIndex = $(this).data('supplier-index');
            if (typeof rowIndex === 'undefined') return;

            const supplier_id = $(`[data-supplier-id="${rowIndex}"]`).val();
            const URL = $(`[data-url-id="${rowIndex}"]`).val();
            const SPN = $(`[data-spn-id="${rowIndex}"]`).val();
            const price = $(`[data-price-id="${rowIndex}"]`).val();

            const anyMeta = [URL, SPN, price].some(v => String(v || '').trim() !== '');
            const hasSupplier = String(supplier_id || '').trim() !== '';

            // fully empty -> ignore
            if (!hasSupplier && !anyMeta) return;

            // meta but no supplier -> client-side validation error
            if (!hasSupplier && anyMeta) {
                errors[`suppliers.${suppliers.length}.supplier_id`] = ['Please select a supplier or remove the row.'];
                return;
            }

            suppliers.push({ supplier_id, URL, SPN, price });
        });

        if (Object.keys(errors).length) {
            const fakeXhr = { responseJSON: { errors } };
            this.formValidator.handleError(fakeXhr);
            return null; // signal invalid
        }

        return suppliers;
    }



    // Initialize uppercase toggler for part name input field
    initializeUppercaseToggle() {
        const $toggleButton = $('#toggle-uppercase-button');
        const $addPartName = $('#addPartName');
        let isUppercase = false;
        let rawInput = '';

        const toggleUppercase = () => {
            isUppercase = !isUppercase;
            if (isUppercase) {
                // Store current value as lowercase
                rawInput = $addPartName.val().toLowerCase();
                $addPartName.on('input.uppercase', function () {
                    // Always update rawInput in lowercase regardless of displayed case
                    rawInput = $(this).val().toLowerCase();
                    // Display in uppercase
                    $(this).val(rawInput.toUpperCase());
                });
                $addPartName.val(rawInput.toUpperCase());
                $toggleButton.addClass('active');
                $toggleButton.text('AA');
            } else {
                $addPartName.off('input.uppercase');
                // Restore raw value as lowercase
                $addPartName.val(rawInput);
                $toggleButton.removeClass('active');
                $toggleButton.text('Aa');
            }
        };

        $toggleButton.off('click').click(() => toggleUppercase());
        $toggleButton.text('Aa');
    }


    // Initialize the "Add Stock" toggler functionality for a new part
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

    // Initialize behaviour of 'Suppliers' and 'Additional Info' buttons
    initializePartEntryButtons() {
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

    // Remove 'active' class from the 'Suppliers' and 'Additional Info' buttons
    resetPartEntryButtons() {
        $('#showSuppliers').removeClass('active');
        $('#showAdvanced').removeClass('active')
    }


    // Select between manual and API entry mode
    initializePartInputButtons() {
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

    // Method to apply selected Mouser part data to the form fields in the modal
    applyMouserPart(part) {
        // 1) Basic fields
        const name = this._buildPartName(part);
        $('#addPartName').val(name);

        // Ensure advanced options are visible so description/comment aren't "hidden success"
        $('#advancedOptions').collapse('show');

        const desc = this._buildShortDescription(part);
        $('#addPartDescription').val(desc);

        const comment = this._buildComment(part);
        $('#addPartComment').val(comment);

        // 2) Hidden fields for later image/datasheet handling
        $('#addPartImageUrl').val(part.ImagePath || '');
        $('#addPartDatasheetUrl').val(part.DataSheetUrl || '');

        // 3) Supplier row: create one row and fill it
        $('#addSuppliers').collapse('show');  // This triggers to show the Suppliers tab, which in turn triggers the dropdown

        //Suppliers need to be AJAX called, then rest of the fields are filled
        this._waitForSupplierSelectize()
            .then(({ idx, selectize }) => {
                // find Mouser option dynamically by label
                const mouserOption = Object.entries(selectize.options)
                    .find(([value, opt]) => String(opt.text || opt.label || '').trim().toLowerCase() === 'mouser');

                if (!mouserOption) {
                    throw new Error('Supplier "Mouser" not found in dropdown options');
                }

                const mouserId = mouserOption[0];
                selectize.setValue(mouserId, true);

                // now fill the rest of the row inputs
                $(`[data-url-id="${idx}"]`).val(part.ProductDetailUrl || '');
                $(`[data-spn-id="${idx}"]`).val(part.MouserPartNumber || '');

                const priceRaw = this._pickWorstTierPrice(part.PriceBreaks);
                const price = this._normalizePriceToDot(priceRaw);

                if (price) {
                    $(`[data-price-id="${idx}"]`).val(price);
                } else {
                    console.warn('No PriceBreaks for part:', part.MouserPartNumber);
                    $(`[data-price-id="${idx}"]`).val(''); // keep empty
                    // Quick UX for now:
                    alert('No price returned by Mouser for this part (maybe obsolete).');
                }
            })
            .catch(err => {
                console.error(err);
                alert(err.message); // temporary UX
            });

        // 4) Footprint guess (phase 1 only)
        const fpGuess = this._guessFootprint(part);
        $('#addPartFootprintGuess').val(fpGuess || '');

        // Optional UX: switch back to manual entry mode so they see the populated name field
        $('#manualEntryButton').trigger('click');
    }

    // Helper methods to extract and format Mouser part data for our form fields
    _buildPartName(part) {
        const m = (part.Manufacturer || '').trim();
        const pn = (part.ManufacturerPartNumber || '').trim();
        if (m && pn) return `${m} ${pn}`;
        return pn || m || '';
    }

    _buildShortDescription(part) {
        // Your field is "brief description"
        // Mouser Description is usually usable but sometimes contains junk like "A 595-..."
        const raw = (part.Description || '').trim();
        if (!raw) return '';

        // Remove trailing Mouser PN patterns like "A 595-XXXX" if present
        const cleaned = raw.replace(/\s+A\s+\d{2,4}-[A-Z0-9\-]+$/i, '').trim();

        return cleaned.length > 160 ? cleaned.slice(0, 157) + '…' : cleaned;
    }

    _buildComment(part) {
        // Comment ideas: sourcing & logistics hints, not “description 2.0”
        const rohs = part.ROHSStatus ? `RoHS: ${part.ROHSStatus}` : null;
        const lead = part.LeadTime ? `Lead: ${part.LeadTime}` : null;
        const pack = this._getAttr(part, ['Verpackung', 'Packaging']);
        const bits = [rohs, lead, pack ? `Pack: ${pack}` : null].filter(Boolean);
        return bits.join(' | ');
    }

    _getAttr(part, names) {
        const attrs = part.ProductAttributes || [];
        const wanted = names.map(n => n.toLowerCase());
        const hit = attrs.find(a => wanted.includes(String(a.AttributeName || '').toLowerCase().trim()));
        return hit ? String(hit.AttributeValue || '').trim() : null;
    }

    _pickWorstTierPrice(priceBreaks) {
        const $lastRow = $('#supplierDataTable tbody tr').last();
        const idx = $lastRow.data('supplier-index');
        console.log('idx', idx, 'price input count', $(`[data-price-id="${idx}"]`).length);

        if (!Array.isArray(priceBreaks) || !priceBreaks.length) return null;

        // pick smallest quantity (usually 1)
        const sorted = [...priceBreaks].sort((a, b) => Number(a.Quantity) - Number(b.Quantity));
        const p = sorted[0]?.Price;
        if (!p) return null;

        // extract just the number, keep comma if you like
        const m = String(p).match(/[\d.,]+/);
        return m ? m[0] : null;  // "0,344"
    }


    _guessFootprint(part) {
        // Very heuristic. We'll improve once we know how you name footprints.
        const guess = this._getAttr(part, ['Package / Case', 'Gehäuse', 'Bauform', 'Gehäuse / Package']);
        if (guess) return guess;

        const img = String(part.ImagePath || '');
        const m = img.match(/(SOT-\d+|SOIC-\d+|TSSOP-\d+|QFN-\d+|QFP-\d+|DFN-\d+|DIP-\d+)/i);
        return m ? m[0].toUpperCase() : null;
    }

    _waitForSupplierSelectize(timeoutMs = 2000) {
        const started = Date.now();

        return new Promise((resolve, reject) => {
            const tick = () => {
                const $row = $('#supplierDataTable tbody tr').last();
                const idx = $row.data('supplier-index');

                if (typeof idx !== 'undefined') {
                    const el = $(`#addPartSupplier-${idx}`).find('select, input').first()[0];
                    const selectize = el?.selectize;

                    if (selectize) {
                        return resolve({ idx, selectize });
                    }
                }

                if (Date.now() - started > timeoutMs) {
                    return reject(new Error('Supplier dropdown not ready (timeout)'));
                }

                setTimeout(tick, 50);
            };

            tick();
        });
    }

    _normalizePriceToDot(raw) {
        if (raw == null) return null;

        // keep only digits, comma, dot
        let s = String(raw).trim().replace(/[^\d.,]/g, '');
        if (!s) return null;

        // Mouser DE: "0,344"  -> "0.344"
        // If you ever get "1.234,56" -> "1234.56"
        const hasComma = s.includes(',');
        const hasDot = s.includes('.');

        if (hasComma && hasDot) {
            // assume dot = thousands, comma = decimal
            s = s.replace(/\./g, '').replace(',', '.');
        } else if (hasComma) {
            s = s.replace(',', '.');
        }

        return s;
    }

    syncMouserTabAccess() {
        const $section = $('#mouserSearchSection');
        const enabled = $section.data('mouser-enabled') === 1 || $section.data('mouser-enabled') === '1';
        const hasKey = $section.data('mouser-has-key') === 1 || $section.data('mouser-has-key') === '1';

        const $btn = $('#mouserSearchButton');
        const $input = $('#mouserPartName');
        const $spinner = $('#mouserLoadingSpinner');
        const $results = $('#mouserSearchResults');

        // reset
        $spinner.addClass('d-none');
        $results.empty();

        if (enabled && hasKey) {
            $btn.prop('disabled', false).removeClass('disabled');
            $input.prop('disabled', false).attr('placeholder', 'Search Part on Mouser');
            return;
        }

        // Mouser not usable: allow clicking the tab, but show CTA panel instead of input usage
        $btn.prop('disabled', false).removeClass('disabled');
        $input.prop('disabled', true);

        const cta = !enabled
            ? { text: 'Enable Mouser Search', href: '/user-settings' }
            : { text: 'Add Mouser API key', href: '/user-settings' };

        const msg = !enabled
            ? 'Mouser Search is disabled in your settings.'
            : 'Mouser Search is enabled, but no API key is stored.';

        $input.attr('placeholder', msg);

        $results.html(`
        <div class="alert alert-secondary d-flex justify-content-between align-items-center mt-3">
            <div>${msg}</div>
            <a class="btn btn-sm btn-primary" href="${cta.href}" target="_blank" rel="noopener">
                ${cta.text}
            </a>
        </div>
    `);
    }



}