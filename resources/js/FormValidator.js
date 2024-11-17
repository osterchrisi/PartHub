export { FormValidator }

class FormValidator {
    constructor($form, config = {}) {
        this.$form = $form || null;
        this.$button = config.button || null;
        this.submitCallback = config.submitCallback || null;

        // Attach validation on instantiation if button and callback are provided
        if (this.$button && this.submitCallback) {
            this.attachValidation();
        }
    }

    attachValidation() {
        this.$button.click((event) => {
            event.preventDefault();
            this.submitFormIfValid();
        });

        //* Currently don't like it anymore, so uncommented the submission upon pressing Enter
        //* Still keeping the preventDefault in, so I don't get browser behaviour that submits forms upon pressing enter (leads to seemingly 'buggy' page reload)
        this.$form.on('keydown', (event) => {
            if (event.key === 'Enter' && !$(document.activeElement).is('.selectized')) {
                event.preventDefault();
                // this.submitFormIfValid();
            }
        });
    }

    submitFormIfValid() {
        this.submitCallback();
    }

    // Method to clear all existing errors before displaying new ones
    clearErrors() {
        this.$form.find('.text-danger').addClass('d-none').text('');
        this.$form.find('input, select, textarea').removeClass('is-invalid');
        this.$form.find('.selectize-control').removeClass('is-invalid');
    }

    // Main method to handle errors during form submission, including supplier row errors
    handleError(xhr) {
        this.clearErrors();

        if (xhr.status === 419) {
            alert('CSRF token mismatch. Please refresh the page and try again.');
        } else if (xhr.status === 403) {
            const response = JSON.parse(xhr.responseText);
            alert(response.message);
        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
            $.each(xhr.responseJSON.errors, (key, messages) => {
                if (key.startsWith('suppliers')) {
                    this.handleSupplierError(key, messages[0]);
                } else {
                    this.handleGeneralError(key, messages[0]);
                }
            });
        } else {
            alert('An error occurred. Please try again.');
        }
    }

    // Method to handle errors for general form fields
    handleGeneralError(key, message) {
        // The error div for general errors has dots replaced by underscores
        const errorDiv = this.$form.find(`#error-${key.replace(/\./g, '_')}`);
        const inputField = this.$form.find(`[name="${key}"]`);

        if (inputField.length) {
            inputField.addClass('is-invalid');
            if (inputField.is('select')) {
                inputField.siblings('.selectize-control').addClass('is-invalid');
            }
        }

        if (errorDiv.length) {
            errorDiv.removeClass('d-none').text(message);
        }
    }

    // Method to handle errors for dynamically added supplier data rows
    handleSupplierError(key, message) {
        const fieldKeyParts = key.split('.');  // Split on dots, e.g., "suppliers.0.price"
        const errorRowIndex = parseInt(fieldKeyParts[1], 10);  // Extract the error row index

        // Capture visible supplier row indexes in the form order they appear
        const visibleRowIndexes = this.$form.find('[data-supplier-index]').map(function () {
            return $(this).data('supplier-index');
        }).get();

        // Check if the error row index is present in visible rows
        const rowIndex = visibleRowIndexes[errorRowIndex];
        if (typeof rowIndex === 'undefined') {
            console.warn(`No visible row found for error at index ${errorRowIndex}`);
            return;
        }

        // Map the error to the corresponding field in the current row
        const fieldName = fieldKeyParts[2];  // e.g., 'price'
        let inputField;

        if (fieldName === 'URL') {
            inputField = this.$form.find(`[data-url-id="${rowIndex}"]`);
        } else if (fieldName === 'SPN') {
            inputField = this.$form.find(`[data-spn-id="${rowIndex}"]`);
        } else if (fieldName === 'price') {
            inputField = this.$form.find(`[data-price-id="${rowIndex}"]`);
        } else if (fieldName === 'supplier_id') {
            inputField = this.$form.find(`#addPartSupplier-${rowIndex} select`);
            if (inputField.length) inputField.siblings('.selectize-control').addClass('is-invalid');
        }

        // Apply invalid class if field exists, and append the error message
        if (inputField && inputField.length) {
            inputField.addClass('is-invalid');
        } else {
            console.warn(`No input field found for error key: ${key}`);
        }

        const generalErrorDiv = this.$form.find('#error-supplier');
        if (generalErrorDiv.length) {
            generalErrorDiv.removeClass('d-none').append(`<p>${message}</p>`);
        }
        else {
            console.log(generalErrorDiv, " not found in ", this.$form);
        }
    }
}