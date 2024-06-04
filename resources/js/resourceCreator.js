export { ResourceCreator };
import { updateInfoWindow } from "./custom";

class ResourceCreator {
  constructor(options) {
    // Options
    this.type = options.type;
    this.endpoint = options.endpoint;
    this.newIdName = options.newIdName;
    this.inputForm = $(options.inputForm);
    this.inputFields = options.inputFields;
    this.inputModal = $(options.inputModal);
    this.addButton = $(options.addButton);
    this.tableRebuildFunction = options.tableRebuildFunction;

    // Handle cancellation of the submit form modal, prevent multiple click listeners
    this.clickListenerAttached = false;
    this.inputModal.on('hidden.bs.modal', () => {
      this.removeAddButtonClickListener()
      this.clickListenerAttached = false;
    });
  }

  requestCreation() {
    const data = {};
    this.inputFields.forEach(field => {
      data[field.name] = $(field.selector).val();
    });

    const token = $('input[name="_token"]').attr('value');

    $.ajax({
      url: this.endpoint,
      type: 'POST',
      data: Object.assign({ _token: token }, data),
      success: (response) => {
        const id = JSON.parse(response)[this.newIdName];  // Get new ID
        updateInfoWindow(this.type, id);                  // Update InfoWindow
        this.hideModal();                                 // Hide Modal
        this.removeAddButtonClickListener();              // Remove Click Listener
        const queryString = window.location.search;
        $.when(this.tableRebuildFunction(queryString)).done(() => {
          $(`tr[data-id="${id}"]`).addClass('selected selected-last');
        });
      },
      error: (xhr) => {
        if (xhr.status === 419) {
          alert('CSRF token mismatch. Please refresh the page and try again.');
        } else {
          alert('An error occurred. Please try again.');
          $(this.inputModal).modal('hide');
          this.removeAddButtonClickListener();
        }
      }
    });
  }

  showModal() {
    this.inputModal.modal('show');
  }

  hideModal() {
    this.inputModal.modal('hide');
  }

  attachAddButtonClickListener() {
    // Check if the click listener has already been attached
    if (!this.clickListenerAttached) {
      this.validateAndSubmitForm(this.inputForm, this.addButton, this.requestCreation.bind(this));
      this.clickListenerAttached = true; // Set the flag to true after attaching the click listener
    }
  }

  removeAddButtonClickListener() {
    this.addButton.off('click');
  }

  validateAndSubmitForm($form, $button, submitCallback, submitArgs = []) {
    // Attach event listeners for form validation and submission
    $button.click((event) => {
      event.preventDefault();
      submitFormIfValid();
    });

    $form.on('keydown', (event) => {
      // Check if the Enter key is pressed and the active element is not the selectized input
      if (event.key === 'Enter' && !$(document.activeElement).is('.selectized')) {
        event.preventDefault(); // Prevent default form submission
        submitFormIfValid();
      }
    });

    // Function to submit the form if it's valid
    const submitFormIfValid = () => {
      if ($form[0].checkValidity()) {
        const result = submitCallback.apply(null, submitArgs);
        return result;
      } else {
        displayFieldValidity();
      }
    };

    // Function to display validity status of required fields
    const displayFieldValidity = () => {
      $form.find('[required]').each(function () {
        const $field = $(this);
        if ($field[0].checkValidity()) {
          $field.removeClass('is-invalid').addClass('is-valid');
        } else {
          $field.removeClass('is-valid').addClass('is-invalid');
        }
      });
    };
  }
}