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

        // Initialize Sortable.js for the image container
        const imageContainer = document.getElementById('imageContainer');

        new Sortable(imageContainer, {
            animation: 150,
            ghostClass: 'sortable-ghost',  // Class name for the ghost element
            onEnd: (event) => {
                this.handleImageReorder(event);
            }
        });

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
                // Set the main picture to the image with the lowest order
                if (response.length > 0) {
                    this.updateMainPicture(response[0]);  // Images are already sorted by order
                }
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

            // Construct image and thumbnail path, file gets served by FileController via associated route
            var thumbnailPath = `/files/images/${this.type}/${image.image_owner_u_id}/${image.associated_id}/thumbnails/` + fileName.replace(/\.[^.]+$/, '') + '.webp';
            var imagePath = `/files/images/${this.type}/${image.image_owner_u_id}/${image.associated_id}/` + fileName;

            // Create the image container
            // Here the image.id is the ID of the image in the DB, not the resource ID (Part, BOM, ...)
            var imageElement = $(`
            <div class="image-wrapper" style="position: relative; display: inline-block;" data-id="${image.id} data-order="${image.order}">
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

    handleImageReorder(event) {
        // Get the new order of images from the image container
        let imageOrder = [];
        $('#imageContainer .image-wrapper').each((index, element) => {
            let imageId = $(element).data('id'); // Get the image ID
            imageOrder.push({
                id: imageId,
                order: index // The new position in the array will be the new order
            });
        });

        // Send the new order to the server
        $.ajax({
            url: `/reorder-images/${this.type}/${this.id}`,
            type: 'POST',
            data: {
                imageOrder: imageOrder,  // Send the array of image IDs with their new order
                _token: $('input[name="_token"]').val()
            },
            success: (mainImage) => {
                // Update the main picture after sorting
                if (mainImage) {
                    this.updateMainPicture(mainImage);
                }
            },
            error: (xhr, status, error) => {
                // console.error('Error updating image order:', error);
            }
        });
    }

    updateMainPicture(image) {
        // Construct file paths for the thumbnail and the full image
        const fileName = image.filename.substring(image.filename.lastIndexOf('/') + 1);
        const thumbnailPath = `/files/images/${this.type}/${image.image_owner_u_id}/${image.associated_id}/thumbnails/` + fileName.replace(/\.[^.]+$/, '') + '.webp';
        const imagePath = `/files/images/${this.type}/${image.image_owner_u_id}/${image.associated_id}/` + fileName;

        $('#mainPicture').attr('src', thumbnailPath); // Update the 'src' attribute of the main picture either to thumbnailPath or imagePath here to chose how it looks. Currently like the original more
        $('#mainPicture').attr('alt', 'Main Picture');

        // Update the 'href' attribute of the main picture link to the full image
        $('#mainPictureLink').attr('href', imagePath);

        // Re-initialize the lightbox for the new main picture
        $('#mainPictureLink').off('click').on('click', (e) => {
            e.preventDefault(); // Prevent default anchor behavior
            $(this).ekkoLightbox(); // Initialize the lightbox
        });
    }





}
