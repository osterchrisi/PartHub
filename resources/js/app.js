import './bootstrap';
import { initializePartsView } from './partsView';
import './partEntry';
import './tables';
import './custom';
import './stockChanges';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const currentView = document.body.getAttribute('data-view');
console.log("data-view = ", currentView);

if (currentView === 'parts'){
    initializePartsView();
}