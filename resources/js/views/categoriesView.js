import {
    bootstrapCategoriesListTable,
    bootstrapTableSmallify,
    defineCategoriesListTableActions
} from "../tables";

import { makeTableWindowResizable } from '../custom.js';

export function initializeCategoriesView() {
    bootstrapCategoriesListTable();

    var $table = $('#categories_list_table');
    var $menu = $('#bom_list_table_menu');
    defineCategoriesListTableActions($table, $menu);

    $('#categories_list_table th[data-field="category_edit"], #categories_list_table td[data-field="category_edit"]').hide();
};