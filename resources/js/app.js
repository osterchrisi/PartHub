import './bootstrap';
import { initializePartsView } from './views/partsView';
import { initializeBomsView } from './views/bomsView';
import { initializeLocationsView } from './views/locationsView';
import { initializeFootprintsView } from './views/footprintsView';
import { initializeSuppliersView } from './views/suppliersView';
import { initializeUserSettingsView } from './views/userSettingsView';
import {
    makeTableWindowResizable,
    clearModalOnHiding,
    focusFirstInputInModals,
    initializePopovers,
    initializeTooltips
} from './custom';
import { InlineTableCellEditor } from './InlineTableCellEditor';

const currentView = document.body.getAttribute('data-view');

// Function to recall and apply saved settings from local storage
function applyLayoutSettings() {
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
    makeTableWindowResizable();
    clearModalOnHiding();
    focusFirstInputInModals();
    initializePopovers();
    initializeTooltips();
    applyLayoutSettings();

    const inlineEditor = new InlineTableCellEditor();
    inlineEditor.enableInlineProcessing();
});