export { FormValidator }

class FormValidator {
    constructor($form, $button) {
        this.$form = $form || null;
        this.$button = $button || null;
    }

    attachValidation(submitCallback) {
        this.$button.click((event) => {
            event.preventDefault();
            this.submitFormIfValid(submitCallback);
        });

        //* Currently don't like it anymore, so uncommented the submission upon pressing Enter
        //* Still keeping the preventDefault in, so I don't get browser behaviour that submits forms upon pressing enter (leads to seemingly 'buggy' page reload)
        this.$form.on('keydown', (event) => {
            if (event.key === 'Enter' && !$(document.activeElement).is('.selectized')) {
                event.preventDefault();
                // this.submitFormIfValid(submitCallback);
            }
        });
    }

    submitFormIfValid(submitCallback) {
        if (this.$form[0].checkValidity()) {
            submitCallback();
        } else {
            this.displayFieldValidity();
        }
    }

    // Method to clear all existing errors before displaying new ones
    clearErrors() {
        this.$form.find('.text-danger').addClass('d-none').text('');
        this.$form.find('input, select, textarea').removeClass('is-invalid');
        $('.selectize-control').removeClass('is-invalid');
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
        const errorDiv = $(`#error-${key.replace(/\./g, '\\.')}`);
        const inputField = $(`[name="${key}"]`);

        // Display invalid inputs
        if (inputField.length) {
            inputField.addClass('is-invalid');
            if (inputField.is('select')) {
                inputField.siblings('.selectize-control').addClass('is-invalid');
            }
        }

        // Show error messages
        if (errorDiv.length) {
            errorDiv.removeClass('d-none').text(message);
        } else {
            console.warn(`No div found for error key: ${key}`);
        }
    }

    // Method to handle errors for dynamically added supplier data rows
    handleSupplierError(key, message) {
        const fieldKeyParts = key.split('.');  // e.g., "suppliers.1.price"
        if (fieldKeyParts[0] === 'suppliers' && fieldKeyParts.length > 2) {
            const rowIndex = parseInt(fieldKeyParts[1], 10);
            const fieldName = fieldKeyParts[2]; // e.g., price

            let inputField;
            if (fieldName === 'URL') {
                inputField = $(`[data-url-id="${rowIndex}"]`);
            } else if (fieldName === 'SPN') {
                inputField = $(`[data-spn-id="${rowIndex}"]`);
            } else if (fieldName === 'price') {
                inputField = $(`[data-price-id="${rowIndex}"]`);
            } else if (fieldName === 'supplier_id') {
                inputField = $(`#addPartSupplier-${rowIndex} select`);
                if (inputField.length) {
                    inputField.addClass('is-invalid');
                    inputField.siblings('.selectize-control').addClass('is-invalid');
                }
            }

            // Add .is-invalid class if input field exists
            if (inputField && inputField.length) {
                inputField.addClass('is-invalid');
            } else {
                console.warn(`No input field found for error key: ${key}`);
            }

            const errorDiv = $(`#error-${key.replace(/\./g, '\\.')}`);
            // Show error messages
            if (errorDiv.length) {
                errorDiv.removeClass('d-none').text(message);
            } else {
                console.warn(`No div found for error key: ${key}`);
            }
        }
    }
}