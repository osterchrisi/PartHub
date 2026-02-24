export { ImageManager }

import { showDeleteConfirmationModal } from "../custom";
import { Layout } from "../User Interface/Layout";

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
        const container = document.getElementById('imageContainer');
        if (!container) return;

        container.replaceChildren();

        response.forEach((image) => {
            const filename = String(image.filename || '');
            const fileName = filename.substring(filename.lastIndexOf('/') + 1);

            const type = String(this.type ?? '');
            const owner = String(image.image_owner_u_id ?? '');
            const assoc = String(image.associated_id ?? '');

            const thumbnailPath =
                `/files/images/${type}/${owner}/${assoc}/thumbnails/` +
                fileName.replace(/\.[^.]+$/, '') +
                '.webp';

            const imagePath =
                `/files/images/${type}/${owner}/${assoc}/` + fileName;

            // wrapper
            const wrapper = document.createElement('div');
            wrapper.className = 'image-wrapper me-3 mb-3';
            wrapper.style.position = 'relative';
            wrapper.style.display = 'inline-block';
            wrapper.dataset.id = String(image.id ?? '');
            wrapper.dataset.order = String(image.order ?? '');

            // link
            const a = document.createElement('a');
            a.href = imagePath;
            a.setAttribute('data-toggle', 'lightbox');
            a.setAttribute('data-gallery', '1');

            // img
            const img = document.createElement('img');
            img.src = thumbnailPath;
            img.alt = 'Thumbnail';

            a.appendChild(img);

            // delete icon
            const del = document.createElement('i');
            del.className = 'bi bi-x-circle delete-image';
            del.dataset.type = type;
            del.dataset.id = String(image.id ?? '');

            wrapper.appendChild(a);
            wrapper.appendChild(del);

            container.appendChild(wrapper);
        });

        // Bind once per refresh, and avoid rebinding lightbox handlers N times in a loop
        // Lightbox: delegate clicks for any future thumbnails too
        $(container)
            .off('click.lightbox')
            .on('click.lightbox', '[data-toggle="lightbox"]', (e) => {
                // If your Lightbox.initialize expects direct click event, keep consistent:
                e.preventDefault();
                Lightbox.initialize.call(e.currentTarget, e);
            });

        // Delete: delegate (so you don't rebind per element)
        $(container)
            .off('click.deleteImage')
            .on('click.deleteImage', '.delete-image', (event) => {
                const imageId = $(event.currentTarget).data('id');
                const imageType = $(event.currentTarget).data('type');
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
        showDeleteConfirmationModal('Are you sure you want to delete this image?', () => {
            var csrfToken = $('input[name="_token"]').val();

            $.ajax({
                url: `/delete-image/${type}/${id}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: (response) => {
                    if (response.success) {
                        Layout.showDeletionConfirmationToast(1, 'image');
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
