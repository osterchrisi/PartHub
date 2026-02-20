import { TableRowManager } from "../Tables/TableRowManager";
import { attachDeleteRowsHandler } from "../toolbar/toolbar";
import { SimpleResourceCreator } from "../Resources/ResourceCreators/SimpleResourceCreator";
import { TableManager } from "../Tables/TableManager";

export function initializeManufacturersView() {
  const manufacturersTableManager = new TableManager({ type: 'manufacturer' });
  manufacturersTableManager.bootstrapTable();
  manufacturersTableManager.defineActions();

  const tableRowManager = new TableRowManager('#manufacturers_list_table', 'manufacturer');
  tableRowManager.loadSelectedRow();

  const manufacturerCreator = new SimpleResourceCreator({
    type: 'manufacturer',
    endpoint: '/manufacturer.create',
    table_name: '#manufacturers_list_table',
    newIdName: 'Manufacturer ID',
    inputForm: '#manufacturerEntryForm',
    inputFields: [
      { name: 'manufacturer_name', selector: '#addManufacturerName' },
      { name: 'manufacturer_contact_name', selector: '#addManufacturerContactName' },
      { name: 'manufacturer_email', selector: '#addManufacturerEmail' },
      { name: 'manufacturer_phone', selector: '#addManufacturerPhone' },
      { name: 'manufacturer_website', selector: '#addManufacturerWebsite' },
      { name: 'manufacturer_comment', selector: '#addManufacturerComment' },
    ],
    inputModal: '#mManufacturerEntry',
    addButton: '#addManufacturer'
  });

  $('#toolbarAddButton').click(function () {
    manufacturerCreator.showModal();
  });

  attachDeleteRowsHandler('manufacturers_list_table', 'manufacturers', 'manufacturer_id', () => manufacturersTableManager.rebuildTable());
}
