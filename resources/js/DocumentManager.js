export { DocumentManager };

import { showDeletionConfirmationToast, showDeleteConfirmation } from "./custom";

/**
 * Class to manage document-related operations such as uploading, displaying, and deleting documents.
 *
 * @class DocumentManager
 */
class DocumentManager {
    /**
    * Creates an instance of DocumentManager.
    *
    * @param {string} type - The type of the entity (e.g., 'part', 'bom', 'location').
    * @param {number} id - The ID of the entity (e.g., Part ID, BOM ID, Location ID).
    */
    constructor(type, id) {
        this.type = type;
        this.id = id; // This ID is the Part, BOM, Location, ... id
    }

    /**
     * Sets up the document container by fetching documents and handling form submission for document upload.
     *
     * @returns {void}
     */
    setupDocumentContainer() {
        this.fetchDocuments(this.type, this.id);

        // Handle form submission
        $('#documentUploadForm').submit((event) => {
            event.preventDefault();

            // Serialize the form data
            var formData = new FormData(event.target);

            // Disable the upload button and show loading animation
            var uploadButton = $(event.target).find('button[type="submit"]');
            var loadingAnimationContainer = $('#loadingAnimationContainer');
            uploadButton.prop('disabled', true);
            loadingAnimationContainer.show();

            // Submit the form data via AJAX
            $.ajax({
                url: `/upload-document/${this.type}/${this.id}`, // Construct the URL dynamically
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: (response) => {
                    this.fetchDocuments(this.type, this.id);
                    loadingAnimationContainer.hide();

                    // Re-enable the upload button after a short delay
                    setTimeout(() => {
                        uploadButton.prop('disabled', false);
                    }, 1000); // 1 second delay
                },
                error: (xhr, status, error) => {
                    console.error(error);
                    loadingAnimationContainer.hide();
                    uploadButton.prop('disabled', false);
                }
            });
        });
    }

    /**
    * Fetches documents for the specified type and ID and updates the document container.
    *
    * @param {string} type - The type of the entity.
    * @param {number} id - The ID of the entity.
    * @returns {void}
    */
    fetchDocuments(type, id) {
        $.ajax({
            url: `/documents/${type}/${id}`,
            type: 'GET',
            success: (response) => {
                this.updateDocuments(response);
            }
        });
    }

    /**
  * Updates the document container with the fetched documents and sets up delete event listeners.
  *
  * @param {Array} response - The array of document objects.
  * @returns {void}
  */
    updateDocuments(response) {
        $('#documentContainer').empty();
        response.forEach((document) => {
            // Use the display_name if available; otherwise, fall back to the filename
            var displayName = document.display_name ? document.display_name : document.filename.substring(document.filename.lastIndexOf('/') + 1);

            // Construct document path
            var documentPath = `/files/documents/${this.type}/${document.document_owner_u_id}/${document.associated_id}/` + document.filename.substring(document.filename.lastIndexOf('/') + 1);

            // Create the document container
            var documentElement = $(`
        <div class="document-wrapper" style="position: relative; display: inline-block;">
            <a href="${documentPath}" target="_blank">
                <i class="bi bi-file-earmark-text"></i> ${displayName}
            </a>
            <i class="bi bi-x-circle delete-document" data-type="${this.type}" data-id="${document.id}"></i>
        </div>
        `);

            $('#documentContainer').append(documentElement);
        });

        // Attach event listener for delete buttons
        $('.delete-document').click((event) => {
            const documentId = $(event.target).data('id'); // Document ID
            const documentType = $(event.target).data('type');
            this.deleteDocument(documentType, documentId);
        });
    }

    /**
    * Deletes a document after confirming with the user.
    *
    * @param {string} type - The type of the entity.
    * @param {number} id - The ID of the document.
    * @returns {void}
    */
    deleteDocument(type, id) {
        showDeleteConfirmation('Are you sure you want to delete this document?', () => {
            var csrfToken = $('input[name="_token"]').val();

            $.ajax({
                url: `/delete-document/${type}/${id}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: (response) => {
                    if (response.success) {
                        showDeletionConfirmationToast(1, 'document');
                        this.fetchDocuments(this.type, this.id);
                    }
                },
                error: (xhr, status, error) => {
                    console.error(error);
                }
            });
        });
    }
}
