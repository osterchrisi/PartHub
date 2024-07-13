import {
    loadActiveTab,
    addActiveTabEventListeners
} from './custom';

import {
    bootstrapBomDetailsTable,
} from './tables';

function allowHtmlTableElementsInPopover() {
    // Allow extra HTML elements for the popover mini stock table
    var myDefaultAllowList = bootstrap.Tooltip.Default.allowList

    // Allow table elements
    myDefaultAllowList.table = []
    myDefaultAllowList.thead = []
    myDefaultAllowList.tr = []
    myDefaultAllowList.td = []
    myDefaultAllowList.tbody = []

    // Allow td elements and data-bs-option attributes on td elements
    myDefaultAllowList.td = ['data-bs-option']

}

export function initializeShowBom() {
    const defaultTab = document.getElementById('bomTabs').dataset.defaultTab; // data-default-tab attribute

    loadActiveTab('bom', defaultTab);
    addActiveTabEventListeners('bom');

    bootstrapBomDetailsTable();
    allowHtmlTableElementsInPopover();

    // Initialize all popovers
    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
    const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));

    // Re-initialize the popovers after toggling a column
    //* This should be possible via the 'column-switch.bs.table' but it never fires...
    $(function () {
        $('#BomDetailsTable').on('post-body.bs.table', function () {
            const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
            const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap
                .Popover(popoverTriggerEl));
        });
    });
}