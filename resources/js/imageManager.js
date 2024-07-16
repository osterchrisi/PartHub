export { ImageManager }

import { showDeletionConfirmationToast, showDeleteConfirmation } from "./custom";

class ImageManager {
    constructor(type, id) {
        this.type = type;
        this.id = id; // This ID is the Part, BOM, Location, ... id
    }

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

    fetchImages(type, id) {
        $.ajax({
            url: `/images/${type}/${id}`,
            type: 'GET',
            success: (response) => {
                this.updateImages(response);
            }
        });
    }

    updateImages(response) {
        $('#imageContainer').empty();
        response.forEach((image) => {
            // Extract the file name from the full path
            var fileName = image.filename.substring(image.filename.lastIndexOf('/') + 1);

            // Construct the thumbnail path by replacing the file name and swapping extension to .webp
            var thumbnailPath = image.filename.replace(fileName, 'thumbnails/' + fileName.replace(/\.[^.]+$/, '') + '.webp');

            // Create the image container
            // Here the image.id is the ID of the image in the DB, not the resource ID (Part, BOM, ...)
            var imageElement = $(`
            <div class="image-wrapper" style="position: relative; display: inline-block;">
                <a href="${image.filename}" data-toggle="lightbox" data-gallery="1">
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
