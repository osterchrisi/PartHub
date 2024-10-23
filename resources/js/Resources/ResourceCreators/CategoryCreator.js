export { CategoryCreator };
import { CategoryTableManager } from "../../../Tables/CategoriesTableManager";
import { ResourceCreator } from "./ResourceCreator";

class CategoryCreator extends ResourceCreator {
    constructor(options) {
        super(options);
        this.categoryId = options.categoryId || null;
        this._shouldUpdateInfoWindow = true;
        this._shouldSelectandSaveNewRow = true;
        this.tableManager = new CategoryTableManager({ type: this.type });
    }

    createTableManager() {
        return new CategoryTableManager({ type: this.type });
      }

    collectFormData (){
        super.collectFormData;
        if (this.categoryId) {
            this.data['parent_category'] = this.categoryId;
          }
    }
}  