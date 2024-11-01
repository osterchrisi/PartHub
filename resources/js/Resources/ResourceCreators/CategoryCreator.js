export { CategoryCreator };
import { CategoryTableManager } from "../../Tables/CategoriesTableManager";
import { ResourceCreator } from "./ResourceCreator";

class CategoryCreator extends ResourceCreator {
    constructor(options) {
        super(options);
        this.parentCategoryId = options.parentCategoryId || null;
        this._shouldUpdateInfoWindow = false;
        this._shouldSelectandSaveNewRow = false;
    }

    createTableManager() {
        return new CategoryTableManager({
            type: this.type,
            resourceCreator: this
        });
    }

    collectFormData() {
        super.collectFormData();
        if (this.parentCategoryId) {
            this.data['parent_category'] = this.parentCategoryId;
        }
    }
}  