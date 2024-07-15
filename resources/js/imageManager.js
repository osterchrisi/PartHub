export { ImageManager }

class ImageManager {
    constructor(type, id) {
        this.type = type;
        this.id = id;
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
                // Check if images exist
                if (response.length > 0) {
                    this.updateImages(response);
                }
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
            var imageElement = $(`
            <div class="image-wrapper" style="position: relative; display: inline-block;">
                <a href="${image.filename}" data-toggle="lightbox" data-gallery="1">
                    <img src="${thumbnailPath}" alt="Thumbnail">
                </a>
                <i class="bi bi-x-circle delete-image" data-type="${this.type}" data-id="${image.id}" style="position: absolute; top: 5px; right: 5px; cursor: pointer;"></i>
            </div>
            `);

            $('#imageContainer').append(imageElement);

            // Initialize Bootstrap 5 Lightbox on all thumbnails
            document.querySelectorAll('[data-toggle="lightbox"]').forEach(el => el.addEventListener('click', Lightbox.initialize));
        });

        // Attach event listener for delete buttons
        $('.delete-image').click((event) => {
            const imageId = $(event.target).data('id');
            const imageType = $(event.target).data('type');
            this.deleteImage(imageType, imageId);
        });
    }

    deleteImage(type, id) {
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            url: `/delete-image/${type}/${id}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: (response) => {
                if (response.success) {
                    this.fetchImages(type, this.id); // Refresh images after deletion
                }
            },
            error: (xhr, status, error) => {
                // Handle any errors that occur during the delete process
                console.error(error);
            }
        });
    }



}
