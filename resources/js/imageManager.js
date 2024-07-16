export { ImageManager }

import { showDeletionConfirmationToast } from "./custom";

class ImageManager {
    constructor(type, id) {
        this.type = type;
        this.id = id; // This ID is the Part, BOM, Location, ... id
    }

    setupImageContainer() {
        // Image handling
        this.fetchImages(this.type, this.id);

        // Handle form submission
        $('#imageUploadForm').submit((event) => {
            // Prevent the default form submission
            event.preventDefault();

            // Serialize the form data
            var formData = new FormData(event.target);

            // Submit the form data via AJAX
            $.ajax({
                url: `/upload-image/${this.type}/${this.id}`, // Construct the URL dynamically
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: (response) => {
                    this.fetchImages(this.type, this.id);
                },
                error: (xhr, status, error) => {
                    // Handle any errors that occur during the upload process
                    console.error(error);
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
        $('#deleteConfirmationModal').modal('show');
    
        // Set up the click handler for the confirmation button
        $('#confirmDeleteButton').off('click').on('click', function() {
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
    
            // Hide the modal after confirmation
            $('#deleteConfirmationModal').modal('hide');
        });
    }
    

}
