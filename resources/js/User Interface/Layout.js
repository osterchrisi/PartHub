export { Layout };

class Layout {
    // Function to recall and apply saved settings from local storage
    static applySavedLayout(currentView) {
        const layoutKey = `layoutSettings_${currentView}`; // Unique key for this page's layout
        const savedLayout = localStorage.getItem(layoutKey);

        if (savedLayout) {
            const layoutData = JSON.parse(savedLayout); // Parse the JSON string

            // Apply table and info window widths if present
            if (layoutData.tableWidth) {
                $('#table-window-container').width(layoutData.tableWidth);
            }

            if (layoutData.infoWidth) {
                $('#info-window').width(layoutData.infoWidth);
            }

            // Apply visibility state to category-window
            if (layoutData.categoryVisible !== undefined) {
                if (layoutData.categoryVisible) {
                    $('#category-window-container').show();
                } else {
                    $('#category-window-container').hide();
                }
            }
        }
    }

    /**
    * Make the table-window and the info-window resizable
    * @return void 
    */
    static makeTableWindowResizable() {
        $('#table-window-container').resizable({
            handles: { 'e': '.table-resize-handle' },
            stop: function () {
                Layout.saveLayoutSettings(); // Save the layout settings after resize
            }
        });

        $('#category-window-container').resizable({
            handles: { 'e': '.category-resize-handle' },
            stop: function () {
                Layout.saveLayoutSettings(); // Save after resizing the category window
            }
        });
    }

    /**
 * Initializes Bootstrap popovers on all elements with the 'data-bs-toggle="popover"' attribute.
 * @returns {void}
 */
    static initializePopovers() {
        const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
        const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));
    }

static showDeletionConfirmationToast(numElements, type) {
    const deleteToast = document.getElementById('tConfirmDelete');

    // Correct singular / plural wording
    if (type == 'parts') {
        if (numElements > 1) {
            type = 'parts';
        } else {
            type = 'part';
        }
    }
    else if (type == 'boms') {
        if (numElements > 1) {
            type = 'BOMs';
        } else {
            type = 'BOM';
        }
    }
    else if (type == 'part_categories') {
        if (numElements > 1) {
            type = 'categories';
        } else {
            type = 'category';
        }
    }
    else if (type == 'image') {
        if (numElements > 1) {
            type = 'images';
        } else {
            type = 'image';
        }
    }

    const numDeletedItemsSpan = document.getElementById('numDeletedItems');
    numDeletedItemsSpan.textContent = numElements.toString();

    const typeSpan = document.getElementById('typeSpan');
    typeSpan.textContent = type.toString();

    const toast = bootstrap.Toast.getOrCreateInstance(deleteToast);
    toast.show();

}

// Initialize Tooltips
static initializeTooltips() {
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl, {
        animation: true,
        delay: { "show": 500, "hide": 100 }
    }));
}

// Function to save sizes and visibility state in local storage
static saveLayoutSettings() {
    const tableWidth = $('#table-window-container').width();
    const infoWidth = $('#info-window').width();
    const categoryVisible = $('#category-window-container').is(':visible');
    const currentView = document.body.getAttribute('data-view');

    const layoutKey = `layoutSettings_${currentView}`; // Create a unique key based on the page

    // Save layout data as an object in local storage
    const layoutData = {
        tableWidth: tableWidth,
        infoWidth: infoWidth,
        categoryVisible: categoryVisible
    };

    localStorage.setItem(layoutKey, JSON.stringify(layoutData)); // Store as a JSON string
}

}