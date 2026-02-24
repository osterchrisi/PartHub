export { DocumentManager };

import { showDeleteConfirmationModal } from "../custom";
import { Layout } from "../User Interface/Layout";

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
        const container = document.getElementById('documentContainer');
        if (!container) return;

        container.replaceChildren();

        response.forEach((doc) => {
            // displayName (untrusted) -> textContent
            const filename = String(doc.filename || '');
            const basename = filename.substring(filename.lastIndexOf('/') + 1);
            const displayName = doc.display_name ? String(doc.display_name) : basename;

            // Build documentPath safely as a string (still untrusted parts, but NOT HTML-parsed)
            const owner = String(doc.document_owner_u_id ?? '');
            const assoc = String(doc.associated_id ?? '');
            const type = String(this.type ?? '');
            const documentPath = `/files/documents/${type}/${owner}/${assoc}/${basename}`;

            // wrapper
            const wrapper = document.createElement('div');
            wrapper.className = 'document-wrapper';
            wrapper.style.position = 'relative';
            wrapper.style.display = 'inline-block';

            // link
            const a = document.createElement('a');
            a.href = documentPath;
            a.target = '_blank';
            a.rel = 'noopener'; // good practice since we're using target=_blank

            const icon = document.createElement('i');
            icon.className = 'bi bi-file-earmark-text';

            a.appendChild(icon);
            a.appendChild(document.createTextNode(' ')); // spacing
            a.appendChild(document.createTextNode(displayName));

            // delete icon
            const del = document.createElement('i');
            del.className = 'bi bi-x-circle delete-document';
            del.dataset.type = type;
            del.dataset.id = String(doc.id ?? '');

            wrapper.appendChild(a);
            wrapper.appendChild(del);

            container.appendChild(wrapper);
        });

        // Bind handler once per refresh
        $('.delete-document').off('click').on('click', (event) => {
            const documentId = $(event.target).data('id');
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
        showDeleteConfirmationModal('Are you sure you want to delete this document?', () => {
            var csrfToken = $('input[name="_token"]').val();

            $.ajax({
                url: `/delete-document/${type}/${id}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: (response) => {
                    if (response.success) {
                        Layout.showDeletionConfirmationToast(1, 'document');
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
