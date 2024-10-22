export { FormValidator }

class FormValidator {
    constructor($form, $button) {
        this.$form = $form;
        this.$button = $button;
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

    displayFieldValidity() {
        this.$form.find('[required]').each(function () {
            const $field = $(this);
            if ($field[0].checkValidity()) {
                $field.removeClass('is-invalid').addClass('is-valid');
            } else {
                $field.removeClass('is-valid').addClass('is-invalid');
            }
        });
    }
}