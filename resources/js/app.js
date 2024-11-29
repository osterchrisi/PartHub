import './bootstrap';
import { initializePartsView } from './views/partsView';
import { initializeBomsView } from './views/bomsView';
import { initializeLocationsView } from './views/locationsView';
import { initializeFootprintsView } from './views/footprintsView';
import { initializeSuppliersView } from './views/suppliersView';
import { initializeUserSettingsView } from './views/userSettingsView';
import {
    clearModalOnHiding,
    focusFirstInputInModals,
} from './custom';
import { InlineTableCellEditor } from './Tables/InlineTableCellEditor';
import { Layout } from './User Interface/Layout';

const currentView = document.body.getAttribute('data-view');

Layout.initialize();

$(document).ready(function () {
    if (currentView === 'parts') {
        initializePartsView();
    }
    else if (currentView === 'boms') {
        initializeBomsView();
    }
    else if (currentView === 'locations') {
        initializeLocationsView();
    }
    else if (currentView === 'footprints') {
        initializeFootprintsView();
    }
    else if (currentView === 'suppliers') {
        initializeSuppliersView();
    }
    else if (currentView === 'user-settings') {
        initializeUserSettingsView();
    }

    // Common to all views
    clearModalOnHiding();
    focusFirstInputInModals();

    const inlineEditor = new InlineTableCellEditor();
    inlineEditor.enableInlineProcessing();

$(document).ready(function () {
    // Attach click event to elements with class "copy-to-clipboard"
    $(document).on('click', '.copy-to-clipboard', function (event) {
        event.preventDefault(); // Prevent default anchor behavior

        const url = $(this).attr('data-url'); // Get the URL from the data attribute
        if (url) {
            console.log("url = ", url);
            navigator.clipboard.writeText(url)
                .then(() => {
                    // Show success feedback using Bootstrap tooltip
                    const $tooltipElement = $(this);
                    $tooltipElement.attr('data-bs-original-title', 'Copied!').tooltip('show');

                    // Reset tooltip text after a short delay
                    setTimeout(() => {
                        $tooltipElement.attr('data-bs-original-title', 'Copy to clipboard').tooltip('hide');
                    }, 2000);
                })
                .catch((err) => {
                    console.error('Failed to copy:', err);
                });
        }
    });
});

});