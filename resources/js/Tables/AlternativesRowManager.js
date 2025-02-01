export { AlternativesRowManager };
import { DropdownManager } from "../DropdownManager";
import { FormValidator } from "../FormValidator";
import { updateInfoWindow } from "../custom";
import { DataFetchService } from "../Services/DataFetchService";

class AlternativesRowManager {
    constructor(options) {
        this.newRowIndex = 0;
        this.inputForm = options.inputForm || null;
        this.$table = $(options.table) || null;

        this.formValidator = new FormValidator($(this.inputForm));
        this.dropdownManager = new DropdownManager();

        // Attach event listener to remove row buttons
        this.removeRowButtonClickListener();
    }

    /**
     * Adds a new alternative data row to the table.
     *
     * @param {number} partId - The ID of the part.
     */
    addAlternativeRow(partId) {
        let tableId = this.$table.attr('id') ? `#${this.$table.attr('id')}` : undefined;

        // Disable the add button temporarily
        if (tableId === '#partAlternativeDataTable') {
            $('#addAlternativeRowBtn-info').prop('disabled', true);
        }

        let newRowIndex = this.newRowIndex;
        let newDropdownDiv = `addPartAlternative-${newRowIndex}`;
        this.newRowIndex++;

        let newRow = `
            <tr data-alternative-index="${newRowIndex}" data-part-id="${partId}">
                <td><div id='${newDropdownDiv}'></div></td>
                <td><button type="button" class="btn btn-sm btn-danger remove-row-btn">
                    <i class="fas fa-trash"></i>
                </button></td>
            </tr>`;

        $(`${tableId} tbody`).append(newRow);

        // Fetch available alternative parts and populate the dropdown
        DataFetchService.getParts().done((parts) => {
            if (parts && parts.length) {
                this.dropdownManager.addPartAlternativeDropdown(parts, newDropdownDiv, newRowIndex);
            } else {
                console.warn("No alternative parts available.");
            }
        }).fail(() => {
            console.error("Failed to fetch alternative parts.");
        });

        // Attach a save Alternative Row handler
        this.saveAlternativeDataRowButtonClickListener(`create-${newRowIndex}`);
    }

    /**
     * Adds a click event listener for the "Add Alternative" button.
     */
    addAlternativeDataRowButtonClickListener(buttonId, partId = null) {
        $(`#${buttonId}`).off('click').on('click', () => {
            this.addAlternativeRow(partId);
        });
    }

    /**
     * Attaches a click listener to remove the row.
     */
    removeRowButtonClickListener() {
        $(document).on('click', '.remove-row-btn', (event) => {
            $(event.currentTarget).closest('tr').remove();
            $('#addAlternativeRowBtn-info').prop('disabled', false);
            this.formValidator.clearErrors();
        });
    }

    /**
     * Attaches an event listener for saving an alternative row.
     */
    saveAlternativeDataRowButtonClickListener(buttonId) {
        let $button = `#${buttonId}`;
        $($button).off('click').on('click', () => {
            this.saveAlternativeDataRow($button);
        });
    }

    /**
     * Sends the selected alternative data to the server.
     *
     * @param {jQuery} button - The button that triggered the save action.
     */
    saveAlternativeDataRow(button) {
        let newAlternativeData = this.getNewAlternativeDataRowData(button);

        if (newAlternativeData.length > 0) {
            let part_id = newAlternativeData[0].part_id;

            let requestData = {
                part_id: part_id,
                alternatives: newAlternativeData.map(alternativeRow => ({
                    alternative_id: alternativeRow.alternative_id
                }))
            };

            // Send the AJAX request to save alternative data
            $.ajax({
                url: `/parts/${part_id}/alternatives`,
                type: 'POST',
                data: JSON.stringify(requestData),
                contentType: "application/json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: (response) => {
                    console.log('Alternative saved successfully:', response);
                    updateInfoWindow('part', part_id);
                    $('#addAlternativeRowBtn-info').prop('disabled', false);
                    this.formValidator.clearErrors();
                },
                error: (xhr) => {
                    this.formValidator.handleError(xhr);
                }
            });
        } else {
            console.log('No alternative data to save');
        }
    }

    /**
     * Collects the alternative data from the new row.
     */
    getNewAlternativeDataRowData(button) {
        let newAlternativeData = [];
        let $row = $(button).closest('tr');
        let rowIndex = $row.data('alternative-index');

        if (typeof rowIndex !== 'undefined') {
            let alternativeRow = {
                part_id: $row.data('part-id'),
                alternative_id: $row.find(`[data-alternative-id="${rowIndex}"]`).val()
            };
            newAlternativeData.push(alternativeRow);
        }
        return newAlternativeData;
    }
}
