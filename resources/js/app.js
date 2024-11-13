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
});