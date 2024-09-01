export { ImageManager }

import { showDeletionConfirmationToast, showDeleteConfirmation } from "./custom";

/**
 * Class to manage image-related operations such as uploading, displaying, and deleting images.
 *
 * @class ImageManager
 */
class ImageManager {
    /**
    * Creates an instance of ImageManager.
    *
    * @param {string} type - The type of the entity (e.g., 'part', 'bom', 'location').
    * @param {number} id - The ID of the entity (e.g., Part ID, BOM ID, Location ID).
    */
    constructor(type, id) {
        this.type = type;
        this.id = id; // This ID is the Part, BOM, Location, ... id
    }

    /**
     * Sets up the image container by fetching images and handling form submission for image upload.
     *
     * @returns {void}
     */
    setupImageContainer() {
        this.fetchImages(this.type, this.id);

        // Handle form submission
        $('#imageUploadForm').submit((event) => {
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
                url: `/upload-image/${this.type}/${this.id}`, // Construct the URL dynamically
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: (response) => {
                    this.fetchImages(this.type, this.id);
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
    * Fetches images for the specified type and ID and updates the image container.
    *
    * @param {string} type - The type of the entity.
    * @param {number} id - The ID of the entity.
    * @returns {void}
    */
    fetchImages(type, id) {
        $.ajax({
            url: `/images/${type}/${id}`,
            type: 'GET',
            success: (response) => {
                this.updateImages(response);
            }
        });
    }

    /**
    * Updates the image container with the fetched images and sets up delete event listeners.
    *
    * @param {Array} response - The array of image objects.
    * @returns {void}
    */
    updateImages(response) {
        $('#imageContainer').empty();
        response.forEach((image) => {
            // Extract the file name from the full path
            var fileName = image.filename.substring(image.filename.lastIndexOf('/') + 1);

            // Construct the thumbnail path using the new route
            var thumbnailPath = `/files/${this.type}/${image.image_owner_u_id}/${image.associated_id}/thumbnails/` + fileName.replace(/\.[^.]+$/, '') + '.webp';
            var imagePath = `/files/${this.type}/${image.image_owner_u_id}/${image.associated_id}/` + fileName;

            // Create the image container
            // Here the image.id is the ID of the image in the DB, not the resource ID (Part, BOM, ...)
            var imageElement = $(`
            <div class="image-wrapper" style="position: relative; display: inline-block;">
                <a href="${imagePath}" data-toggle="lightbox" data-gallery="1">
                    <img src="${thumbnailPath}" alt="Thumbnail">
                </a>
                <i class="bi bi-x-circle delete-image" data-type="${this.type}" data-id="${image.id}"></i>
            </div>
            `);

            $('#imageContainer').append(imageElement);

            // Initialize Bootstrap 5 Lightbox on all thumbnails
            document.querySelectorAll('[data-toggle="lightbox"]').forEach(el => el.addEventListener('click', Lightbox.initialize));
        });

        // Attach event listener for delete buttons
        $('.delete-image').click((event) => {
            const imageId = $(event.target).data('id'); // Image ID
            const imageType = $(event.target).data('type');
            this.deleteImage(imageType, imageId);
        });
    }


    /**
    * Deletes an image after confirming with the user.
    *
    * @param {string} type - The type of the entity.
    * @param {number} id - The ID of the image.
    * @returns {void}
    */
    deleteImage(type, id) {
        showDeleteConfirmation('Are you sure you want to delete this image?', () => {
            var csrfToken = $('input[name="_token"]').val();

            $.ajax({
                url: `/delete-image/${type}/${id}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: (response) => {
                    if (response.success) {
                        showDeletionConfirmationToast(1, 'image');
                        this.fetchImages(this.type, this.id);
                    }
                },
                error: (xhr, status, error) => {
                    console.error(error);
                }
            });
        });
    }


}
