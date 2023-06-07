import './bootstrap';
import { initializePartsView } from './partsView';
import { initializeBomsView } from './bomsView';
import './partEntry';
import './tables';
import './custom';
import './stockChanges';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const currentView = document.body.getAttribute('data-view');
console.log("data-view = ", currentView);

$(document).ready(function () {
    if (currentView === 'parts') {
        initializePartsView();
    }
    else if (currentView === 'boms') {
        initializeBomsView();
    }
});